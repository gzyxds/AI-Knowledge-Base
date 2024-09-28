<?php

namespace app\common\service\ai\search;

use app\common\cache\KeyPoolCache;
use app\common\enum\PoolEnum;
use app\common\enum\SearchEnum;
use app\common\model\search\AiSearchRecord;
use app\common\service\ai\ChatService;
use Exception;
use function Qcloud\Cos\endWith;

class TiangongService
{
    private string $appKey;     // 令牌
    private string $appSecret;  // 密钥
    private int $timestamp;     // 时间
    private string $sign;       // 授权签名
    private array $headers;     // 请求信息

    // 请求信息
    protected string $baseUrl = 'https://api.singularity-ai.com';
    protected string $model; // 模型: search=简易,copilot=增强,research=研究
    protected string $type;  // 类型: all=全网, doc=文档, scholar=学术
    protected string $ask;   // 问题

    // 用户ID
    protected int $userId;

    // 密钥对象
    protected mixed $keyPoolServer = null;

    // 搜索内容
    protected array $content = [];

    // 上下文
    protected array $context = [];

    protected string $section = '';

    /**
     * @notes 初始化
     * @throws Exception
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->keyPoolServer = (new KeyPoolCache(SearchEnum::TIANGONG, PoolEnum::TYPE_SEARCH, SearchEnum::TIANGONG));
        $keyConfig = $this->keyPoolServer->getKey();
        if (empty($keyConfig)) {
            throw new Exception('请在后台配置key');
        }

        $this->appKey = $keyConfig['key'];
        $this->appSecret = $keyConfig['secret'];
        $this->timestamp = time();
        $this->sign = md5($this->appKey . $this->appSecret . $this->timestamp);

        $this->headers = [
            'sign: ' . $this->sign,
            'app_key: ' . $this->appKey,
            'timestamp: ' . $this->timestamp,
            'Accept: application/json',
            'Content-Type: application/json'
        ];
    }

    /**
     * @notes 发起搜索
     * @param string $model 模型
     * @param string $type  类型
     * @param string $ask   问题
     * @param bool $probe   是否追问
     * @return void
     * @throws Exception
     * @author fzr
     */
    public function query(string $model, string $type, string $ask, bool $probe = false): void
    {
        ignore_user_abort(true);
        $this->model = $model;
        $this->type = $type;
        $this->ask = $ask;

        // 请求参数
        $data = [
            'content' => $ask,
            'stream_resp_type' => 'delta',
            'chat_history' => $probe ? $this->context() : []
        ];

        // 请求接口
        switch ($model) {
            case 'copilot': # 增强搜索
                $url = $this->baseUrl . '/sky-saas-search/api/v1/copilot';
                break;
            case 'research': # 研究搜索
                $url = $this->baseUrl . '/sky-saas-search/api/v1/search/research';
                if ($type == 'scholar') {
                    $data['is_scholar'] = true;
                }
                break;
            default: # 基础搜索
                $url = $this->baseUrl . '/sky-saas-search/api/v1/search';
                break;
        }

        // 流式处理
        $response = false;
        $callback = function ($ch, $data) use (&$response) {
            $result = @json_decode($data);

            // 检索报错处理
            if ($result) {
                $code = $result->code;
                $codeMsg = $result->code_msg;
                $error = $this->keyPoolServer->takeDownKey($codeMsg, $this->baseUrl);
                $response = "tg: $error ($code)";
                return 1;
            }

            $response = true;
            if (!connection_aborted()) {
                $this->parseStreamData($data);
                return strlen($data);
            } else {
                return 1;
            }
        };

        // 发起请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,290);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('请求出错!');
        } elseif (is_string($response)) {
            $error = $this->keyPoolServer->takeDownKey($response, $this->baseUrl);
            throw new Exception($error);
        }
    }

    /**
     * @notes 获取搜索内容
     * @return array
     * @author fzr
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @notes 获取上下文
     * @return array
     * @author fzr
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @notes 搜索上下文
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    private function context(): array
    {
        $results = (new AiSearchRecord())
            ->where(['user_id'=>$this->userId])
            ->limit(3)
            ->order('id desc')
            ->select()
            ->toArray();

        $messages = [];
        foreach ($results as $item) {
            $messages[] = ['role'=>'user', 'content'=>$item['ask']];
            $messages[] = ['role'=>'bot', 'content'=>$item['markdown']];
        }

        $this->context = $messages;
        return $messages;
    }

    /**
     * @notes 处理数据流
     * @param $stream
     * @return void
     * @author fzr
     */
    private function parseStreamData($stream): void
    {
        $dataLists = explode("\n\n", $stream);
        foreach ($dataLists as $streamData) {
            if (str_contains(trim($streamData, "\n"), 'data: [DONE]')) {
                return;
            }

            $data = str_replace("data: ", "", $streamData);
            $data = @json_decode($data, true);

            // 解析出来的是数据不完整情况，
            if ($streamData and empty($data)) {
                if (!$this->section) {
                    $this->section = $streamData;
                    continue;
                } else {
                    $this->section.= $streamData;
                    $data = str_replace("data: ", "", $this->section);
                    $data = json_decode($data, true);
                    if (empty($data)) {
                        continue;
                    }
                    $this->section = '';
                }
            }

            // 处理数据
            $type = $data['type'] ?? 0;
            $target = $data['target'] ?? '';
            $cardType = $data['card_type'] ?? '';

            switch ($cardType) {
                case 'action':
                case 'search_mode':
                    $record = ['text' => $data['arguments'][0]['messages'][0]['text']];
                    break;
                case 'search_query':
                    $record = $data['arguments'][0]['messages'][0]['searchKeys'];
                    $this->content[$cardType] = $record;
                    break;
                case 'search_result':
                    $sourceAttributions = $data['arguments'][0]['messages'][0]['sourceAttributions'] ?? [];
                    $sources = [];
                    foreach ($sourceAttributions as $v) {
                        $sources[] = [
                            'doc_type' => $v['doc_type'],
                            'seeMoreUrl' => $v['seeMoreUrl'],
                            'showName' => $v['showName'],
                            'image' => $v['image'],
                            'snippet' => $v['snippet'],
                            'title' => $v['title'],
                            'publishDate' => $v['publishDate'],
                            'pictures' => $v['pictures'] ?? ''
                        ];
                    }
                    $record = $sources;
                    $result = $this->content['search_result'] ?? [];
                    $this->content['search_result'] = array_merge($result, $record);
                    break;
                case 'markdown':
                case 'expand_query':
                    $record = $data['arguments'][0]['messages'][0]['text'];
                    $content = $this->content['markdown'] ?? '';
                    $this->content['markdown'] = $content . $record;
                    break;
                case 'outline':
                case 'outline_json':
                case 'related_events':
                case 'related_people':
                case 'related_organizations':
                    $sources = [];
                    $relatedEvents = $data['arguments'][0]['messages'][0]['related_events'] ?? [];
                    $relatedPeople = $data['arguments'][0]['messages'][0]['related_people'] ?? [];
                    $sourceAttributions = $data['arguments'][0]['messages'][0]['sourceAttributions'];
                    foreach ($sourceAttributions as $v) {
                        $sources[] = [
                            'doc_type' => $v['doc_type'],
                            'seeMoreUrl' => $v['seeMoreUrl'],
                            'showName' => $v['showName'],
                            'image' => $v['image'],
                            'snippet' => $v['snippet'],
                            'title' => $v['title'],
                            'publishDate' => $v['publishDate'],
                            'pictures' => $v['pictures'] ?? ''
                        ];
                    }
                    $record = [
                        'text' => $data['arguments'][0]['messages'][0]['text'],
                        'card_type' => $cardType,
                        'source_attributions' => $sources
                    ];
                    if ($relatedEvents) {
                        $record['related_events'] = $relatedEvents;
                    }
                    if ($relatedEvents) {
                        $record['related_people'] = $relatedPeople;
                    }
                    $this->content[$cardType] = $record;
                    break;
                case 'suggestion':
                    $record = $data['arguments'][0]['messages'][0]['suggestedResponses'] ?? [];
                    $result = $this->content[$cardType] ?? [];
                    $this->content[$cardType] = array_merge($result, $record);
                    break;
                default:
                    $record = [];
                    if ($data) {
                        $cardType = $data['card_type'] ?: 'done';
                    } else {
                        break;
                    }
            }

            if (!$cardType and !$target) {
               continue;
            }

            ChatService::AiSearchOutput($cardType, $target, $type, $record);
        }
    }
}