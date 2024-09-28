<?php

namespace app\common\service\ai\chat;

use app\common\cache\KeyPoolCache;
use app\common\enum\ChatEnum;
use app\common\service\ai\ChatService;
use Exception;

class QwenService
{
    protected string $baseUrl      = 'https://dashscope.aliyuncs.com';
    protected array $config        = [];                         // 配置参数
    protected string $channel      = 'qwen';                     // 渠道模型
    protected string $model        = '';                         // 对话模型
    protected string $apiKey       = '';                         // 接口密钥
    protected bool $outputStream   = true;                       // 流式输出

    protected string $secretKey    = '';                          // API密钥
    protected string $accessToken  = '';                          // 授权令牌

    protected int $contextNum          = 0;                       // 上下文数
    protected float $temperature       = 0.85;                    // 词汇属性: [默认0.8, 范围(0, 1.0), 不能为0]
    protected float $repetitionPenalty = 1.0;                     // 重复惩罚
    protected float $presencePenalty   = 0.0;                     // 存在惩罚
    protected bool $enableSearch       = false;                   // 互联网搜索
    protected array $messages          = [];                      // 上下文

    protected array $content       = [];                          // 回复的内容
    protected array $usage         = [];                          // token使用量

    protected mixed $keyPoolServer = null;                        // Key池对象

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
        $this->model             = trim($this->config['model']);
        $this->contextNum        = intval($this->config['context_num']);
        $this->temperature       = floatval($this->config['temperature']??0.85);
        $this->repetitionPenalty = floatval($this->config['repetition_penalty']??1.0);
        $this->presencePenalty   = intval($this->config['presence_penalty']??0);
        $this->enableSearch      = boolval($this->config['enable_search']??0);

        // 替换代理域名
        if ($this->config['agency_api'] ?? '') {
            $this->baseUrl = $this->config['agency_api'];
        }

        // 设置请求域名
        $this->baseUrl .= '/api/v1/services/aigc/text-generation/generation';

        // 获取密钥Key
        $this->keyPoolServer = (new KeyPoolCache($chatConfig['model_id'], ChatEnum::MODEL_TYPE_CHAT));
        $this->apiKey = $this->keyPoolServer->getKey();
        if (empty($this->apiKey)) {
            throw new Exception('请在后台配置key');
        }
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
        $data = [
            'model' => $this->model,
            'input' => [
                'messages' => $messages,
            ],
            'parameters' => [
                'temperature'        => $this->temperature,
                'incremental_output' => false,
                'result_format'      => 'message',
                'repetition_penalty' => $this->repetitionPenalty,
                'presence_penalty'   => $this->presencePenalty,
                'enable_search'      => $this->enableSearch
            ],
        ];

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT,300);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('通义千问: 请求超时');
        }

        return $this->parseResponseData($response);
    }

    /**
     * @notes SSE对话请求
     * @param array $messages
     * @return $this
     * @throws Exception
     * @author fzr
     */
    public function chatSseRequest(array $messages): self
    {
        $this->messages = $messages;
        ignore_user_abort(true);
        $header = [
            'Authorization: Bearer '.$this->apiKey,
            'Content-Type: application/json',
            'X-DashScope-SSE: enable',
        ];
        $data = [
            'model' => $this->model,
            'input' => [
                'messages' => $messages,
            ],
            'parameters' => [
                'temperature'        => $this->temperature,
                'incremental_output' => $this->outputStream,
                'repetition_penalty' => $this->repetitionPenalty,
                'presence_penalty'   => $this->presencePenalty,
                'enable_search'      => $this->enableSearch
            ],
        ];
        $response = true;
        $callback = function ($ch, $data) use (&$content,&$response,&$total){
            $result = @json_decode($data);
            if (!$result) {
                $dataArr = explode("\n", $data);
                foreach ($dataArr as $val){
                    if(!str_contains($val, 'data:')){
                        continue;
                    }
                    $error = str_replace("data:", "", $val);
                    $error = json_decode($error, true);
                    if (isset($error['code'])) {
                        throw new Exception('通义千问:'.$error['message'] ?? $error['code']);
                    }
                }
            }
            if (isset($result->code)) {
                $error = $this->keyPoolServer->takeDownKey($result->error->message, $this->baseUrl);
                $response = '通义千问:'.$error;
            } else {
                $this->parseStreamData($data);
            }
            // 客户端没断开
            if (connection_aborted()) {
                return 1;
            }

            return strlen($data);
        };

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT,101);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if(true !== $response){
            throw new Exception('通义千问:'.$response);
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
                'str_length'        => mb_strlen($promptContent . $this->content[0]),
                'prompt_tokens'     => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens'      => $promptTokens + $completionTokens
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
        $responseData = json_decode($response,true);
        if (empty($responseData['output'])) {
            throw new Exception($responseData['message']);
        }

        $this->content[0] = $responseData['output']['choices'][0]['message']['content']??'';
        $this->usage = [
            'prompt_tokens'     => $responseData['usage']['input_tokens']  ?? 0,
            'completion_tokens' => $responseData['usage']['output_tokens'] ?? 0,
            'total_tokens'      => $responseData['usage']['total_tokens']  ?? 0
        ];

        $index = 0;
        $event = 'finish';
        $finish = 'stop';
        return ChatService::parseReturnSuccess(
            $event,
            $responseData['request_id'],
            $this->content[0]??'',
            $index,
            $this->model,
            $finish,
            false,
            $responseData['usage']
        );
    }

    /**
     * @notes 解析SSE数据
     * @param $stream
     * @author fzr
     */
    private function parseStreamData($stream): void
    {
        $dataLists = explode("\n", $stream);
        $chatEvent = 'chat';
        foreach ($dataLists as $data){
            if(!str_contains($data, 'data:')){
                continue;
            }

            $data = str_replace("data:", "", $data);
            $data = json_decode($data, true);

            // 解析到数据是空的,可能是数据丢失问题
            if (empty($data) || !is_array($data)) {
                continue;
            }

            $index = 0;
            $finishReason = null;
            $id = $data['request_id'] ?? '';
            $streamContent = $data['output']['text']  ?? '';
            $isEnd = $data['output']['finish_reason'] ?? '';
            if('stop' === $isEnd || 'length' === $isEnd){
                //$finishReason = true;
                $this->usage = [
                    'prompt_tokens'     => $data['usage']['input_tokens']  ?? 0,
                    'completion_tokens' => $data['usage']['output_tokens'] ?? 0,
                    'total_tokens'      => $data['usage']['total_tokens']  ?? 0
                ];
            }

            // 结束标识
            if ($finishReason) {
                $chatEvent = 'finish';
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
                $finishReason,
                $this->outputStream
            );
        }
    }
}