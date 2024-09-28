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
use think\facade\Log;
use WpOrg\Requests\Requests;

/**
 * 科大讯飞chat服务类
 */
class XunfeiService
{
    protected array $config          = [];                           // 配置参数
    protected string $channel        = 'xunfei';                     // 渠道模型
    protected string $model          = '';                           // 对话模型
    protected string $baseUrl = 'https://spark-api-open.xf-yun.com'; // 请求地址
    protected bool $outputStream   = true;                           // 流式输出

    protected string $apiSecret      = '';                           // API密钥
    protected string $apikey         = '';                           // API令牌
    protected mixed $keyPoolServer = null;                           // Key池对象

    protected int $contextNum        = 0;                            // 上下文数
    protected float $temperature     = 0.5;                          // 词汇属性: 取值为[0,1],默认为0.5
    protected float $topK            = 4;                            // 话题属性: 取值为[1,6],默认为4
    protected array $messages        = [];                           // 上下文
    protected array $headers         = [];                           // 请求头

    protected array $content         = [];                           // 返回数据
    protected array $usage           = [];                           // 返回token使用量

    /**
     * @notes 初始化
     * @param array $chatConfig
     * @throws Exception
     * @author fzr
     */
    public function __construct(array $chatConfig)
    {
        // 获取当前模型的渠道
        $this->channel = $chatConfig['channel'];
        $this->config  = $chatConfig;

        // 是否流式输出 (SSE有效)
        $this->outputStream = $chatConfig['outputStream'] ?? true;

        // 设置基础参数
        $this->model        = $this->config['model'] ?? 'general';
        $this->contextNum   = intval($this->config['context_num']??0);
        $this->temperature  = floatval($this->config['temperature']??0.5);
        $this->topK         = floatval($this->config['top_k']??4);

        // 获取密钥Key
        $this->keyPoolServer = (new KeyPoolCache($chatConfig['model_id'], ChatEnum::MODEL_TYPE_CHAT, $this->channel));
        $keyConfig = $this->keyPoolServer->getKey();
        if (empty($keyConfig)) {
            throw new Exception('请在后台配置key');
        }

        // 代理域名
        if (!empty($this->config['agency_api'])) {
            $this->baseUrl = trim($this->config['agency_api']);
        }

        // 获取密钥
        $this->apikey    = trim($keyConfig['key']);
        $this->apiSecret = trim($keyConfig['secret']);

        $this->headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' .$this->apikey.':'.$this->apiSecret,
        ];
    }

    /**
     * @notes HTTP对话请求
     * @param array $messages
     * @return array
     * @throws Exception
     * @author fzr
     */
    public function chatHttpRequest(array $messages): array
    {

        $this->messages = $messages;
        $url = $this->baseUrl .'/v1/chat/completions';
        $data = [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => $this->temperature,
            'top_k'       => $this->topK,
            'stream'      => false
        ];

        $headers  = [];
        foreach ($this->headers as $item) {
            $h = explode(': ', $item);
            $headers[$h[0]] = $h[1];
        }

        // 设置超时时间
        $options['timeout'] = 300;
        $response = Requests::post($url, $headers, json_encode($data), $options);
        return $this->parseResponseData($response);
    }

    /**
     * @notes SSE对话请求
     * @param array $messages
     * @return $this
     * @throws @\WebSocket\BadOpcodeException
     * @author fzr
     */
    public function chatSseRequest(array $messages): self
    {
        ignore_user_abort(true);
        $this->messages = $messages;
        $url = $this->baseUrl .'/v1/chat/completions';
        $data = [
            'model'       => $this->model,
            'messages'    => $messages,
            'stream'      => true,
            'temperature' => $this->temperature,
            'top_k'       => $this->topK,
        ];

        $response = true;
        $callback = function ($ch, $data) use (&$response, &$total, &$a){
            $result = @json_decode($data);

            if ($result and isset($result["message"])) {
                $error = $this->keyPoolServer->takeDownKey($result["message"], $this->baseUrl);
                $response = 'xunfei:'. $error;
                return 1;
            } else{
                $this->parseStreamData($data);
            }

            // 客户端断开
            if(connection_aborted()){
                return 1;
            }

            return strlen($data);
        };

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,100);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if(true !== $response){
            if (!$response) {
                throw new Exception('Xunfei: 请求出错!');
            } else {
                $error = $this->keyPoolServer->takeDownKey($response, $this->baseUrl);
                throw new Exception($error);
            }
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
            $promptTokens     = gpt_tokenizer_count($promptContent);
            $completionTokens = gpt_tokenizer_count($this->content[0]);
            return [
                'total_tokens'      => $promptTokens + $completionTokens,
                'prompt_tokens'     => $promptTokens,
                'completion_tokens' => $completionTokens,
                'str_length'        => mb_strlen($promptContent . $this->content[0])
            ] ?? [];
        } else {
            $this->usage['str_length'] = mb_strlen($promptContent . $this->content[0]);
            return $this->usage;
        }
    }

    /**
     * @notes 解析HTTP数据
     * @param mixed $response
     * @return array
     * @throws Exception
     * @author fzr
     */
    private function parseResponseData(mixed $response): array
    {
        $responseData = json_decode($response->body,true);
        if (isset($responseData['error'])) {
            $message = $responseData['error']['message'];
            $type    = $responseData['error']['type'];
            $code    = $responseData['error']['code'];
            $param   = $responseData['error']['param'];
            $error = $this->keyPoolServer->takeDownKey($message, $this->baseUrl);
            return ChatService::parseReturnError(false, $error, $code, $this->model, $type, $param);
        }

        $this->usage   = $responseData['usage'] ?? [];
        $this->content = [$responseData['choices'][0]['message']['content']];
        return $responseData;
    }

    /**
     * @notes 解析SSE数据
     * @param $stream
     * @author fzr
     */
    private function parseStreamData($stream): void
    {
        $dataLists = explode("\n\n", $stream);
        $chatEvent = 'chat';
        foreach ($dataLists as $data){
            if(!str_contains($data, 'data:')){
                continue;
            }

            if(str_contains($data, 'data: [DONE]')){
                continue;
            }

            $data = str_replace("data: ", "", $data);
            $data = json_decode($data, true);

            // 解析到数据是空的、可能是数据丢失问题
            if (empty($data) || !is_array($data)) {
                Log::write("讯飞数据异常:".$stream);
                continue;
            }

            $id            = $data['id'] ?? '';
            $index         = $data['choices'][0]['index'] ?? 0;
            $streamContent = $data['choices'][0]['delta']['content'] ?? '';
            //$finishReason  = $data['message']??null;

            if (!isset($data['choices'][0]['delta']['content'])) {
                Log::write('讯飞数据可能丢失:'.$stream);
                continue;
            }
            $contents = $this->content[$index] ?? '';
            $this->content[$index] = $contents.$streamContent;

            // 给前端发送流数据
            ChatService::parseReturnSuccess(
                $chatEvent,
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