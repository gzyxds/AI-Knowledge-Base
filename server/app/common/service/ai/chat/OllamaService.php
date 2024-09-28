<?php

namespace app\common\service\ai\chat;

use app\common\cache\KeyPoolCache;
use app\common\enum\ChatEnum;
use app\common\service\ai\ChatService;
use Exception;
use think\facade\Log;
use WpOrg\Requests\Requests;

class OllamaService
{
    protected array $config            = [];                        // 配置参数
    protected string $channel          = '';                        // 渠道模型
    protected string $model            = '';                        // 对话模型
    protected string $apiKey           = '';                        // 接口密钥
    protected string $baseUrl          = '';                        // 请求地址
    protected bool $outputStream       = true;                      // 流式输出

    protected int $contextNum          = 0;                         // 上下文数
    protected float $temperature       = 0.7;                       // 词汇属性
    protected float $repeat_penalty    = 1.1;                       // 重复惩罚
    protected int $mirostat           = 0;                          // 困惑控制
    protected float $mirostat_eta      = 0.1;                       // 响应效率
    protected array $messages          = [];                        // 上下文内容

    protected array $headers           = [];                        // 请求头值
    protected array $content           = [];                        // 回复的内容
    protected array $usage             = [];                        // 使用Token

    protected bool $isCheckKey = false;                             // key校验
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
        $this->model          = trim($this->config['model']);
        $this->contextNum     = intval($this->config['context_num']??0);
        $this->temperature    = floatval($this->config['temperature']??0.7);
        $this->repeat_penalty = floatval($this->config['repeat_penalty']??1.1);
        $this->mirostat       = intval($this->config['mirostat']??0);
        $this->mirostat_eta   = floatval($this->config['mirostat_eta']??0.1);

        // 替换代理域名
        if ($this->config['agency_api'] ?? '') {
            $this->baseUrl = $this->config['agency_api'];
        }

        // 设置请求头值
        if(!$this->baseUrl){
            throw new Exception('请在后台配置请求域名');
        }

        // 获取密钥Key
        if (isset($chatConfig['check_key']) and $chatConfig['check_key']) {
            $this->isCheckKey = true;
            $this->keyPoolServer = (new KeyPoolCache($chatConfig['model_id'], ChatEnum::MODEL_TYPE_CHAT));
            $this->apiKey = $this->keyPoolServer->getKey();
            if (empty($this->apiKey)) {
                throw new Exception('请在后台配置key');
            }
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
        $url = $this->baseUrl . '/api/chat';

        $data = [
            'stream'         => false,
            'model'          => $this->model,
            'messages'       => $messages,
            'temperature'    => $this->temperature,
            'repeat_penalty' => $this->repeat_penalty,
            'mirostat'       => $this->mirostat,
            'mirostat_eta'   => $this->mirostat_eta
        ];

        // 设置超时时间
        $options['timeout'] = 300;
        $response = Requests::post($url, $this->headers, json_encode($data), $options);
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
        ignore_user_abort(true);
        $this->messages = $messages;
        $url = $this->baseUrl .'/api/chat';

        $data = [
            'stream'         => true,
            'model'          => $this->model,
            'messages'       => $messages,
            'temperature'    => $this->temperature,
            'repeat_penalty' => $this->repeat_penalty,
            'mirostat'       => $this->mirostat,
            'mirostat_eta'   => $this->mirostat_eta
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
            if (isset($result->error)) {
                if ($this->isCheckKey) {
                    $this->keyPoolServer->takeDownKey($result->error);
                }
                $response = 'ollama: '.$result->error;
                return 1;
            }

            $this->parseStreamData($data);
            $response = true;
            return strlen($data);
        };

        $headers = [];
        if ($this->isCheckKey) {
            foreach ($this->headers as $key => $item) {
                $headers[] = trim($key . ': ' . $item);
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,102);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if(true !== $response){
            $msg = $response === false ? '请求超时,请检查部署的服务是否正常!' : $response;
            throw new Exception($msg);
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
     * @notes 获取消耗的tokens
     * @author fzr
     */
    public function getUsage(): array
    {
        $promptContent = '';
        foreach ($this->messages as $item) {
            $promptContent .= $item['content'];
            //$promptContent .= "\n\n\n\n";
        }

        if ($this->usage) {
            $this->usage['str_length'] = mb_strlen($promptContent . $this->content[0]);
            return $this->usage;
        } else {
            $completionTokens = gpt_tokenizer_count($this->content[0]);
            $promptTokens     = gpt_tokenizer_count($promptContent);
            return [
                'prompt_tokens'     => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens'      => $promptTokens + $completionTokens,
                'str_length'        => mb_strlen($promptContent . $this->content[0])
            ] ?? [];
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
            $error = $responseData['error'];
            if ($this->isCheckKey) {
                $error = $this->keyPoolServer->takeDownKey($error, $this->baseUrl);
            }
            return ChatService::parseReturnError(false, $error, 0, $this->model);
        }

        $this->content = [$responseData['message']['content']];
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
            $data = json_decode($stream, true);
            if (empty($data) || !is_array($data)) {
                throw new Exception('ollama data empty');
            }
        } catch (Exception $e) {
            Log::write('数据异常了: '.$e->getMessage());
        }

        $index         = 0;
        //$finishReason  = $data['done'] ?? '';
        $finishReason  = null;
        $streamContent = $data['message']['content'] ?? '';
        $id            = $data['id'] ?? '';

        $contents = $this->content[$index] ?? '';
        $this->content[$index] = $contents.$streamContent;

        $chatEvent = !$finishReason ? 'chat' : 'finish';
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