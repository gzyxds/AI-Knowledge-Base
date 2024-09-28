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
 * chatGpt服务类
 */
class OpenaiService
{
    protected array $config            = [];                        // 配置参数
    protected string $channel          = 'openai';                  // 渠道模型
    protected string $model            = '';                        // 对话模型
    protected string $apiKey           = '';                        // 接口密钥
    protected string $baseUrl          = 'https://api.openai.com';  // 请求地址
    protected bool $outputStream       = true;                      // 流式输出

    protected int $contextNum          = 0;                         // 上下文数
    protected float $temperature       = 0;                         // 词汇属性
    protected float $presencePenalty   = 0;                         // 话题属性
    protected float $frequencyPenalty  = 0;                         // 重复属性
    protected array $messages          = [];                        // 上下文内容

    protected array $headers           = [];                        // 请求头值
    protected array $content           = [];                        // 回复的内容
    protected array $usage             = [];                        // 使用Token

    protected mixed $keyPoolServer = null;                          // Key池对象

    /**
     * @notes 初始化
     * @param array $chatConfig
     * @throws Exception
     */
    public function __construct(array $chatConfig)
    {
        // 获取当前模型的渠道
        $this->config  = $chatConfig;
        $this->channel = $chatConfig['channel'];

        // 是否流式输出 (SSE有效)
        $this->outputStream = $chatConfig['outputStream'] ?? true;

        // 设置基础参数
        $this->model            = $this->config['model'] ?? 'gpt-3.5-turbo';
        $this->contextNum       = intval($this->config['context_num']??0);
        $this->temperature      = floatval($this->config['temperature']??1.0);
        $this->presencePenalty  = floatval($this->config['presence_penalty']??0);
        $this->frequencyPenalty = floatval($this->config['frequency_penalty']??0);

        // 获取密钥Key
        $this->keyPoolServer = (new KeyPoolCache($chatConfig['model_id'], ChatEnum::MODEL_TYPE_CHAT));
        $this->apiKey = $this->keyPoolServer->getKey();
        if (isset($chatConfig['check_key']) and $chatConfig['check_key']) {
            if (empty($this->apiKey)) {
                throw new Exception('请在后台配置key');
            }
        }

        if ($this->channel == 'baichuan') {
            $this->baseUrl = 'https://api.baichuan-ai.com';
        }

        // 替换代理域名
        if ($this->config['agency_api'] ?? '') {
            $this->baseUrl = $this->config['agency_api'];
        }

        // 设置请求头值
        $this->headers['Accept']  = 'application/json';
        $this->headers['Content-Type']  = 'application/json';
        $this->headers['Authorization'] = 'Bearer '.trim($this->apiKey);
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
        $reqUrl = $this->baseUrl . '/v1/chat/completions';
        $data = [
            'model'    => $this->model,
            'messages' => $messages,
            'stream'   => false,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presencePenalty,
            'frequency_penalty' => $this->frequencyPenalty
        ];

        // 设置超时时间
        $options['timeout'] = 300;
        $response = Requests::post($reqUrl, $this->headers, json_encode($data), $options);
        return $this->parseResponseData($response);
    }

    /**
     * @notes SSE对话请求
     * @param array $messages
     * @return OpenaiService
     * @throws Exception
     * @author fzr
     */
    public function chatSseRequest(array $messages): self
    {
        ignore_user_abort(true);
        $this->messages = $messages;
        $reqUrl = $this->baseUrl . '/v1/chat/completions';
        $data = [
            'model'             => $this->model,
            'messages'          => $messages,
            'stream'            => true,
            'temperature'       => $this->temperature,
            'presence_penalty'  => $this->presencePenalty,
            'frequency_penalty' => $this->frequencyPenalty
        ];

        $response = false;
        $callback = function ($ch, $data) use (&$response, &$total, &$a){
            $result = @json_decode($data);
            // 如果不是true,验证流数据是否正确,第一次没报错,第一次后都直接走这段代码

            if (false !== $response) {
                $this->parseStreamData($data);
                // 客户端没断开
                if (!connection_aborted()) {
                    return strlen($data);
                } else {
                    return 1;
                }
            }

            // 第一次流执行的流程
            // openAi报错
            if (isset($result->error)) {
                $error = $this->keyPoolServer->takeDownKey($result->error->message, $this->baseUrl);
                $response = 'openai:'.$result->error->message ? $error : $result->error->type;
                return 1;
            }

            // api2d的报错
            if ((isset($result->object) && 'error' == $result->object)) {
                $error = $this->keyPoolServer->takeDownKey($result->message, $this->baseUrl);
                $response = 'api2d:'.$error;
                return 1;
            }

            $this->parseStreamData($data);
            $response = true;
            return strlen($data);
        };

        $headers = [];
        foreach ($this->headers as $key => $item) {
            $headers[] = trim($key) . ': ' . trim($item);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $reqUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,110);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if(true !== $response){
            if ($response === false) {
                throw new Exception('请求出错!');
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
            if (is_array($item['content'])) {
                $promptContent .= $item['content'][0]['text'];
            } else {
                $promptContent .= $item['content'];
            }
            //$promptContent .= "\n\n\n";
        }

        if (!$this->usage) {
            $promptTokens     = gpt_tokenizer_count($promptContent);
            $completionTokens = gpt_tokenizer_count($this->content[0]);
            return [
                'prompt_tokens'     => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens'      => $promptTokens + $completionTokens,
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
            $code    = $responseData['error']['code'];
            $type    = $responseData['error']['type'];
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
                Log::write("数据异常:".$stream);
                continue;
            }

            $id            = $data['id'] ?? '';
            $index         = $data['choices'][0]['index'] ?? 0;
            //$finishReason  = $data['choices'][0]['finish_reason'] ?? '';
            $finishReason  = null;
            $streamContent = $data['choices'][0]['delta']['content'] ?? '';

            // 结束标识
            if('stop' == $finishReason){
                $chatEvent = 'finish';
            }else{
                if (!isset($data['choices'][0]['delta']['content'])) {
                    Log::write('数据可能丢失:'.$stream);
                    continue;
                }
                $contents = $this->content[$index] ?? '';
                $this->content[$index] = $contents.$streamContent;
            }

            // 给前端发送流数据
            ChatService::parseReturnSuccess(
                $chatEvent,
                $id,
                $streamContent,
                $index,
                $this->model,
                $finishReason,
                $this->outputStream
            );
        }
    }
}