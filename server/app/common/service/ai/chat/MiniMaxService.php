<?php

namespace app\common\service\ai\chat;

use app\common\cache\KeyPoolCache;
use app\common\enum\ChatEnum;
use app\common\service\ai\ChatService;
use Exception;
use WpOrg\Requests\Requests;

class MiniMaxService
{
    protected array $config            = [];                         // 配置参数
    protected string $channel          = 'minimax';                  // 渠道模型
    protected string $model            = '';                         // 对话模型
    protected string $apiKey           = '';                         // 接口密钥
    protected string $groupId          = '';                         // 接口密钥
    protected string $baseUrl          = 'https://api.minimax.chat'; // 请求地址
    protected bool $outputStream       = true;                       // 流式输出

    protected int $contextNum          = 0;                          // 上下文数
    protected float $temperature       = 0;                          // 词汇属性
    protected array $messages          = [];                         // 上下文内容

    protected array $headers           = [];                         // 请求头值
    protected array $content           = [];                         // 回复的内容
    protected array $usage             = [];                         // 使用Token

    protected mixed $keyPoolServer = null;                           // Key池对象
    protected string $requestId    = '';

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
        $this->model            = $this->config['model'] ?? 'abab5.5-chat';
        $this->contextNum       = intval($this->config['context_num']??0);
        $this->temperature      = floatval($this->config['temperature']??1.0);

        // 获取密钥Key
        $this->keyPoolServer = (new KeyPoolCache($chatConfig['model_id'], ChatEnum::MODEL_TYPE_CHAT, $this->channel));
        $keyConfig = $this->keyPoolServer->getKey();

        $this->apiKey  = trim($keyConfig['key'] ?? '');
        $this->groupId = trim($keyConfig['appid'] ?? '');

        if (isset($chatConfig['check_key']) and $chatConfig['check_key']) {
            if (empty($keyConfig)) {
                throw new Exception('请联系管理员检查秘钥');
            }
        }

        // 替换代理域名
        if ($this->config['agency_api'] ?? '') {
            $this->baseUrl = $this->config['agency_api'];
        }

        // 设置请求头值
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
        $reqUrl = $this->baseUrl . '/v1/text/chatcompletion_v2?GroupId='.$this->groupId;
        $this->messages = $messages;

        $data = [
            'stream'      => false,
            'model'       => $this->model,
            'messages'    => $this->messages,
            'temperature' => $this->temperature
        ];

        // 设置超时时间
        $options['timeout'] = 300;
        $response = Requests::post($reqUrl, $this->headers, json_encode($data), $options);
        return $this->parseResponseData($response);
    }

    /**
     * @notes SSE对话请求
     * @param array $messages
     * @return MiniMaxService
     * @throws Exception
     * @author fzr
     */
    public function chatSseRequest(array $messages): self
    {
        ignore_user_abort(true);
        $reqUrl = $this->baseUrl . '/v1/text/chatcompletion_v2?GroupId='.$this->groupId;
        $this->messages = $messages;

        $data = [
            'stream'            => true,
            'model'             => $this->model,
            'messages'          => $messages,
            'temperature'       => $this->temperature
        ];

        $response = true;
        $callback = function ($ch, $data) use (&$response, &$total){
            $result = @json_decode($data);

            $dataLength = strlen($data);
            if(isset($result->base_resp)){
                $error = $this->keyPoolServer->takeDownKey($result->base_resp->status_msg, $this->baseUrl);
                $response = 'miniMax:'.$error ? $error : $result->base_resp->status_msg;
                return 1;
            }

            $this->parseStreamData($data);

            // 客户端没断开
            if(connection_aborted()){
                return 1;
            }

            return $dataLength;
        };

        $headers  = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->apiKey
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $reqUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT,300);//设置300秒超时
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if(true !== $response){
            if ($response === false) {
                throw new Exception('请求出错(miniMax)!');
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
            ];
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
        if (isset($responseData['base_resp']) and $responseData['base_resp']['status_code'] != 0) {
            $code    = $responseData['base_resp']['status_code'];
            $message = $responseData['base_resp']['status_msg'];
            $error = $this->keyPoolServer->takeDownKey($message, $this->baseUrl);
            return ChatService::parseReturnError(false, $error, $code, $this->model);
        }
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
        try {
            $stream = str_replace("data: ", "", $stream);
            $data = json_decode($stream, true);
            $this->requestId = $data['request_id'] ?? $this->requestId;

            if(isset($data['base_resp'])){
                $baseResp = $data['base_resp'];
                if(0 != $baseResp['status_code']){
                    throw new Exception($baseResp['status_msg']);
                }
            }

            $chatEvent = 'chat';
            foreach ($data['choices'] as $index => $choices) {
                $streamDelta = $choices['delta'] ?? [];
                $streamContent = $streamDelta['content'] ?? '';
                $content = $this->content[$index] ?? '';

                $finishReason = null;
                if (isset($choices['finish_reason'])) {
                    $chatEvent = 'finish';
                    //$finishReason = $choices['finish_reason'];
                }

                // 给前端发送流数据
                ChatService::parseReturnSuccess(
                    $chatEvent,
                    $this->requestId,
                    $streamContent,
                    $index,
                    $this->model,
                    $finishReason,
                    $this->outputStream
                );

                $this->content[$index] = $content.$streamContent;
            }
        } catch (Exception $e) {
            dump($e->getMessage());exit;
        }
    }
}