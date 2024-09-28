<?php

namespace app\common\service\ai\chat;

use app\common\cache\KeyPoolCache;
use app\common\enum\ChatEnum;
use app\common\service\ai\ChatService;
use Exception;
use think\facade\Log;
use WpOrg\Requests\Requests;

class DoubaoService
{
    protected array $config            = [];                        // 配置参数
    protected string $channel          = 'doubao';                  // 渠道模型
    protected string $model            = '';                        // 对话模型
    protected string $apiKey           = '';                        // 接口密钥
    protected string $baseUrl          = '';                        // 请求地址
    protected bool $outputStream       = true;                      // 流式输出

    protected int $contextNum          = 0;                         // 上下文数
    protected float $temperature       = 0;                         // 词汇属性
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
        $this->outputStream = ($chatConfig['outputStream'] ?? true);

        // 设置基础参数
        $this->model            = $this->config['model'] ?? '';
        $this->contextNum       = (int) ($this->config['context_num']??0);
        $this->temperature      = (float) ($this->config['temperature']??0.9);
        $this->frequencyPenalty = (float) $this->config['frequency_penalty']??0;

        // 获取密钥Key
        $this->keyPoolServer = (new KeyPoolCache($chatConfig['model_id'], ChatEnum::MODEL_TYPE_CHAT));
        $this->apiKey = $this->keyPoolServer->getKey();
        if (isset($chatConfig['check_key']) and $chatConfig['check_key']) {
            if (empty($this->apiKey)) {
                throw new Exception('请在后台配置key');
            }
        }

        // 替换代理域名
        $this->baseUrl = 'https://ark.cn-beijing.volces.com/api/v3';
        if ($this->config['agency_api'] ?? '') {
            $this->baseUrl = $this->config['agency_api'];
        }

        // 设置请求头值
        $this->headers['Content-Type']  = 'application/json';
        $this->headers['Authorization'] = 'Bearer ' . trim($this->apiKey);
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
        $url = $this->baseUrl.'/chat/completions';
        $data = [
            'model'             => $this->model,
            'stream'            => false,
            'messages'          => $messages,
            'temperature'       => $this->temperature,
            'frequency_penalty' => $this->frequencyPenalty
        ];

        // 设置超时时间
        $options['timeout'] = 300;
        $response = Requests::post($url, $this->headers, json_encode($data), $options);
        return $this->parseResponseData($response);
    }

    /**
     * @notes SSE对话请求
     * @param array $messages
     * @return DoubaoService
     * @throws Exception
     * @author fzr
     */
    public function chatSseRequest(array $messages): self
    {
        ignore_user_abort(true);
        $this->messages = $messages;
        $url = $this->baseUrl.'/chat/completions';
        $data = [
            'model'             => $this->model,
            'stream'            => true,
            'messages'          => $messages,
            'temperature'       => $this->temperature,
            'frequency_penalty' => $this->frequencyPenalty
        ];

        $response = true;
        $callback = function ($ch, $data) use (&$content,&$response,&$total){
            $result = @json_decode($data);
            if (isset($result->error)) {
                $error = $this->keyPoolServer->takeDownKey($result->error->message, $this->baseUrl);
                $response = 'doubao:'.$result->error->message ? $error : $result->error->type;
            }else{
                $this->parseStreamData($data);
            }

            // 客户端没断开
            if(connection_aborted()){
                return 1;
            }

            return strlen($data);
        };

        $headers = [];
        foreach ($this->headers as $key => $item) {
            $headers[] = $key . ': ' . trim($item);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,301);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if(true !== $response){
            if ($response === false) {
                throw new Exception('Doubao: 请求出错!');
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
            //$promptContent .= "\n\n";
            //$promptContent .= "\n";
        }

        if (!$this->usage) {
            $promptTokens     = gpt_tokenizer_count($promptContent);
            $completionTokens = gpt_tokenizer_count($this->content[0]);
            return [
                'prompt_tokens'     => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens'      => $promptTokens + $completionTokens,
                'str_length'        => mb_strlen($this->content[0] . $promptContent)
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
            $param   = $responseData['error']['param'];
            $type    = $responseData['error']['type'];
            $code    = $responseData['error']['code'];
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
        $chatEvent = 'chat';
        $dataLists = explode("\n\n", $stream);
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
                Log::write('豆包模型数据异常:'.$stream);
                continue;
            }

            $id            = $data['id'] ?? '';
            $index         = (int) ($data['choices'][0]['index'] ?? 0);
            $streamContent = $data['choices'][0]['delta']['content'] ?? '';
            $finishReason  = $data['choices'][0]['finish_reason'] ?? '';

            // 结束标识
            if('stop' == $finishReason){
                $chatEvent = 'finish';
            }else{
                if (!isset($data['choices'][0]['delta']['content'])) {
                    Log::write('豆包模型数据可能丢失:'.$stream);
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
                null,
                $this->outputStream
            );
        }
    }
}