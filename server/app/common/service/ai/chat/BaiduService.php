<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\common\service\ai\chat;

use app\common\cache\KeyPoolCache;
use app\common\enum\ChatEnum;
use app\common\service\ai\ChatService;
use Exception;
use think\facade\Cache;
use WpOrg\Requests\Requests;

/**
 * 文心一言服务类
 */
class BaiduService
{
    protected array $config        = [];                         // 配置参数
    protected string $channel      = 'baidu';                    // 渠道模型
    protected string $model        = '';                         // 对话模型
    protected string $apiKey       = '';                         // 接口密钥
    protected string $baseUrl      = 'https://aip.baidubce.com'; // 请求地址
    protected bool $outputStream   = true;                       // 流式输出

    protected string $secretKey    = '';                          // API密钥
    protected string $accessToken  = '';                          // 授权令牌

    protected int $contextNum      = 0;                           // 上下文数
    protected float $temperature   = 0.8;                         // 词汇属性: [默认0.8, 范围(0, 1.0), 不能为0]
    protected float $penaltyScore  = 1.0;                         // 减少重复生成的现象: [1.0, 2.0]
    protected bool $disableSearch  = false;                       // 是否强制关闭实时搜索功能
    protected array $messages      = [];                          // 上下文

    protected array $content       = [];                          // 回复的内容
    protected array $usage         = [];                          // token使用量

    protected mixed $keyPoolServer = null;                          // Key池对象

    /**
     * @notes 初始化
     * @param array $chatConfig
     * @throws Exception
     * @author fzr
     */
    public function __construct(array $chatConfig)
    {
        // 当前模型渠道
        $this->channel = $chatConfig['channel'];
        $this->config  = $chatConfig;

        // 是否流式输出 (SSE有效)
        $this->outputStream = $chatConfig['outputStream'] ?? true;

        // 设置基础参数
        $this->model        = trim($this->config['model']);
        $this->contextNum   = intval($this->config['context_num']);
        $this->temperature  = floatval($this->config['temperature']??0.8);
        $this->penaltyScore = floatval($this->config['penalty_score']??1.0);
        $this->disableSearch = boolval($this->config['disableSearch']??0);

        // 参数兼容处理
        if ($this->temperature <= 0) { $this->temperature = 0.1; }
        if ($this->temperature >= 1) { $this->temperature = 1.0; }

        // 设置请求域名
        $this->baseUrl .= '/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/';
        if (!empty($this->config['agency_api'])) {
            $this->baseUrl = $this->config['agency_api'] . '/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/';
        }

        // 获取密钥Key
        $this->keyPoolServer = (new KeyPoolCache($chatConfig['model_id'], ChatEnum::MODEL_TYPE_CHAT, $this->channel));
        $keyConfig = $this->keyPoolServer->getKey();
        if (empty($keyConfig)) {
            throw new Exception('请在后台配置key');
        }

        // 请求地址
        $versions = ['ERNIE-Bot-turbo'=>'eb-instant', 'ERNIE-Bot 4.0'=>'completions_pro', 'ERNIE-Bot-8K'=>'ernie_bot_8k'];
        $this->baseUrl .= $versions[$this->model]??'completions';

        // 获取密钥
        $this->apiKey    = $keyConfig['key'];
        $this->secretKey = $keyConfig['secret'];
        $this->getAccessToken();
    }

    /**
     * @notes HTTP对话请求
     * @param array $messages
     * @return array
     * @author fzr
     */
    public function chatHttpRequest(array $messages): array
    {
        $this->messages = $messages;
        $data = [
            'messages'       => $messages,
            'temperature'    => $this->temperature,
            'penalty_score'  => $this->penaltyScore,
            'disable_search' => $this->disableSearch
        ];

        if ($messages[0]['role'] == 'system') {
            if (count($messages) <= 1) {
                $data['messages'] = ['role'=>'user', 'content'=>$messages[0]['content']];
            } else {
                $data['system'] = $messages[0]['content'];
                array_shift($data['messages']);
            }
        }

        $headers  = [
            'Content-Type: application/json',
        ];
        $url = $this->baseUrl . '?access_token='.$this->accessToken;

        $options['timeout'] = 300;
        $response = Requests::post($url, $headers, json_encode($data), $options);
        return $this->parseResponseData($response);
    }

    /**
     * @notes SSE对话请求
     * @param array $messages
     * @return BaiduService
     * @throws Exception
     * @author fzr
     */
    public function chatSseRequest(array $messages): self
    {
        ignore_user_abort(true);
        $this->messages = $messages;
        $data = [
            'stream'      => true,
            'messages'    => $messages,
            'temperature' => $this->temperature,
            'penalty_score'  => $this->penaltyScore,
            'disable_search' => $this->disableSearch
        ];

        if ($messages[0]['role'] == 'system') {
            if (count($messages) <= 1) {
                $data['messages'] = ['role'=>'user', 'content'=>$messages[0]['content']];
            } else {
                $data['system'] = $messages[0]['content']??'';
                array_shift($data['messages']);
            }
        }

        $response = true;
        $callback = function ($ch, $data) use (&$content,&$response,&$total){
            $result = @json_decode($data);
            if (isset($result->error_code)) {
                $response = $result->error_msg;
            } else {
                $this->parseStreamData($data);
            }

            // 客户端没断开
            if (!connection_aborted()) {
                return strlen($data);
            } else {
                return 1;
            }
        };

        $headers  = [
            'Content-Type: application/json',
        ];
        $url = $this->baseUrl . '?access_token='.$this->accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT,100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if (true !== $response) {
            if ($response == 'Access token expired') {
                $cacheMd5 = md5($this->apiKey.$this->secretKey);
                Cache::delete('baidu_access_token_'. $cacheMd5);
            }
            throw new Exception((string)$response);
        }
        return $this;
    }

    /**
     * @notes 获取回复内容
     * @return array
     * @author fzr
     */
    public function getReplyContent(): array
    {
        return $this->content;
    }

    /**
     * 获取消耗的tokens
     * @author fzr
     */
    public function getUsage(): array
    {
        $promptContent = '';
        foreach ($this->messages as $item) {
            $promptContent .= $item['content'];
            //$promptContent .= "\n\n\n";
        }

        if (!$this->usage) {
            $completionTokens = gpt_tokenizer_count($this->content[0]);
            $promptTokens     = gpt_tokenizer_count($promptContent);
            return [
                'prompt_tokens'     => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens'      => $promptTokens + $completionTokens,
                'str_length'        => mb_strlen($promptContent . trim($this->content[0]))
            ] ?? [];
        } else {
            $this->usage['str_length'] = mb_strlen($promptContent . $this->content[0]);
            return $this->usage;
        }
    }

    /**
     * @notes 请求鉴权Token
     * @return void
     * @throws Exception
     * @author fzr
     */
    private function getAccessToken(): void
    {
        // 读取缓存
        $cacheMd5 = md5($this->apiKey.$this->secretKey);
        $tokenCache = Cache::get('baidu_access_token_'.$cacheMd5);
        if ($tokenCache) {
            $this->accessToken = $tokenCache['access_token'];
            return;
        }

        // 设置参数
        $url = 'https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id='.$this->apiKey.'&client_secret='.$this->secretKey;
        $options['timeout'] = 20;
        $header = [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];

        // 发起请求
        $response = Requests::post($url,$header,[],$options);
        $result = json_decode($response->body,true);
        if (isset($result['error'])) {
            throw new Exception($result['error_description'] ?? '鉴权失败');
        }

        // 缓存AccessToken
        $this->accessToken = $result['access_token'];
        $result['expires_time'] = time() + $result['expires_in'];
        Cache::set('baidu_access_token_'. $cacheMd5, $result);
    }

    /**
     * @notes 解析HTTP数据
     * @param $response
     * @return array
     * @author fzr
     */
    private function parseResponseData($response): array
    {
        $responseData = json_decode($response->body,true);
        if (isset($responseData['error_code'])) {
            $message = $responseData['error_msg'];
            $code    = $responseData['error_code'];
            return ChatService::parseReturnError(false, $message, $code, $this->model);
        }

        $index  = 0;
        $finish = 'stop';
        $object = 'finish';
        $this->usage = $responseData['usage'];
        $this->content = [$responseData['result']];
        return ChatService::parseReturnSuccess(
            $object,
            $responseData['id'],
            $responseData['result'],
            $index,
            $this->model,
            $finish,
            false,
            $this->usage
        );
    }

    /**
     * @notes 解析SSE数据
     * @param $stream
     * @author fzr
     */
    private function parseStreamData($stream): void
    {
        $event = 'chat';
        $dataLists = explode("\n\n", $stream);
        foreach ($dataLists as $data){
            if(!str_contains($data, 'data:')){
                continue;
            }

            // 解析数据并转换成Json格式
            $data = str_replace("data: ", "", $data);
            $data = json_decode($data, true);

            // 解析到数据是空的,可能是数据丢失问题
            if (empty($data) || !is_array($data)) {
                continue;
            }

            $index   = 0;
            $finish  = null;
            $id      = $data['id']??'';
            $streamContent = $data['result'] ?? '';

            if(true === $data['is_end'] || true === $data['need_clear_history'] ){
                $finish = 'stop';
            }

            // 结束标识
            if ($finish != null) {
                $this->usage = $data['usage'];
                $event = 'finish';
            }

            // 保存数据
            $contents = $this->content[$index] ?? '';
            $this->content[$index] = $contents.$streamContent;

            // 给前端发送流数据
            ChatService::parseReturnSuccess(
                $event,
                $id,
                $streamContent,
                $index,
                $this->model,
                null,
                $this->outputStream
            );
        }
    }
}