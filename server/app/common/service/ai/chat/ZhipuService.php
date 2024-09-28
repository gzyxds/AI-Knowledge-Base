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
use Firebase\JWT\JWT;
use Exception;
use WpOrg\Requests\Requests;

/**
 * 智谱chat服务类
 */
class ZhipuService
{
    protected array $config            = [];                         // 配置参数
    protected string $channel          = 'openai';                   // 渠道模型
    protected string $model            = '';                         // 对话模型
    protected string $apiKey           = '';                         // 接口密钥
    protected string $token            = '';                         // 授权令牌
    protected string $baseUrl          = 'https://open.bigmodel.cn'; // 请求地址
    protected bool $outputStream       = true;                       // 流式输出

    protected int $contextNum        = 0;                            // 上下文数
    protected float $temperature     = 0.95;                         // 词汇属性
    protected bool $doSample         = true;                         // 采样策略
    protected array $messages        = [];                           // 上下文

    protected array $content         = [];                           // 回复的内容
    protected array $usage           = [];                           // token使用量

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
        $this->model       = $this->config['model'];
        $this->contextNum  = intval($this->config['context_num']??0);
        $this->temperature = floatval($this->config['temperature']??0.95);
        $this->doSample    = boolval($this->config['do_sample']??1);

        // 参数兼容处理
        if ($this->temperature <= 0) { $this->temperature = 0.1; }
        if ($this->temperature >= 1) { $this->temperature = 0.99; }

        // 替换代理域名
        if (!empty($this->config['agency_api'])) {
            $this->baseUrl = trim($this->config['agency_api']);
        }

        // 获取密钥Key
        $this->keyPoolServer = (new KeyPoolCache($chatConfig['model_id'], ChatEnum::MODEL_TYPE_CHAT));
        $this->apiKey = $this->keyPoolServer->getKey();
        if (empty($this->apiKey)) {
            throw new Exception('请在后台配置key');
        }

        // 通过jwt包生成令牌
        $this->token = $this->generateToken($this->apiKey, 86400 * 7);

        // 获取模型的请求接口
        $this->baseUrl .= '/api/paas/v4/chat/completions';
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
            'model'       => $this->model,
            'stream'      => false,
            'temperature' => $this->temperature,
            'do_sample'   => $this->doSample,
            'messages'    => $messages
        ];

        $headers  = [
            'Content-Type' => 'application/json',
            'Authorization' => $this->token
        ];

        $options['timeout'] = 300;
        $response = Requests::post($this->baseUrl, $headers, json_encode($data), $options);
        return $this->parseResponseData($response);
    }

    /**
     * @notes SSE对话请求
     * @param array $messages
     * @return ZhipuService
     * @throws Exception
     * @author fzr
     */
    public function chatSseRequest(array $messages): self
    {
        ignore_user_abort(true);
        $this->messages = $messages;
        $data = [
            'model'       => $this->model,
            'stream'      => true,
            'temperature' => 0.1,
            'do_sample'   => $this->doSample,
            'messages'    => $messages
        ];

        $resCode = 0;
        $response = true;
        $callback = function ($ch, $data) use (&$content, &$response, &$total, &$resCode) {
            $result = @json_decode($data);
            if (isset($result->error)) {
                $response = $result->error->message;
            } elseif (isset($result->success) && !$result->success) {
                $response = $result->msg;
                $resCode  = $result->code;
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
            'Authorization: ' . $this->token
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT,100);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
        curl_close($ch);

        if(true !== $response){
            throw new Exception((string)$response, $resCode);
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
                'str_length'        => mb_strlen(trim($promptContent) . $this->content[0])
            ] ?? [];
        } else {
            $this->usage['str_length'] = mb_strlen($promptContent . $this->content[0]);
            return $this->usage;
        }
    }

    /**
     * @notes jwt生成token
     * @param string $apiKey  (智谱apikey格式：{id}.{secret})
     * @param int $expireTime (过期时间)
     * @return string
     * @throws Exception
     * @author fzr
     */
    private function generateToken(string $apiKey, int $expireTime): string
    {
        [$id, $secret] = explode('.', $apiKey);

        // 构造token的payload部分
        $nowMs = round(microtime(true) * 1000);

        // Claim构成 {"api_key":"id","exp":1617847620516,"timestamp":1617847616516}
        $payload = [
            'api_key'   => $id,
            'exp'       => ($nowMs + ($expireTime * 1000)),
            'timestamp' => $nowMs
        ];

        return JWT::encode($payload, $secret, 'HS256',null, ['sign_type'=> 'SIGN']);
    }

    /**
     * @notes 解析HTTP数据
     * @param mixed $response
     * @return array
     * @author fzr
     */
    private function parseResponseData(mixed $response): array
    {
        $responseData = json_decode($response->body,true);

        $this->content[0] = $responseData['choices'][0]['message']['content']??'';
        $this->usage      = $responseData['usage']??[];

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
        $index       = 0;
        $chatEvent   = 'chat';
        $streamLists = explode("\n\n", $stream);
        foreach ($streamLists as $streamData) {
            $data = str_replace("data: ", "", $streamData);
            $data = json_decode($data, true);
            // 解析出来的是数据不完整情况，
            if (empty($data)) {
                // 则按拼接上一个不完整数据,如果是第一个不完整数据,则先保存起来
                if (empty($this->section)) {
                    $this->section = $streamData;
                    continue;
                } else {
                    $this->section.= $streamData;
                    $data = str_replace("data: ", "", $this->section);
                    $data = json_decode($data, true);
                    // 数据不完整,继续接收接下来的数据,拼接数据
                    if (empty($data)) {
                        continue;
                    }
                    $this->section = '';
                }
            }

            // 处理数据
            $streamContent = $data['choices'][0]['delta']['content'];
            $finishReason  = $data['choices'][0]['finish_reason'] ?? '';
            if('stop' == $finishReason) {
                $this->usage = $data['usage']??[];
                $chatEvent = 'finish';
            }

            $id = $data['id'];
            $contents = $this->content[0] ?? '';
            $this->content[0] = $contents.$streamContent;

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
