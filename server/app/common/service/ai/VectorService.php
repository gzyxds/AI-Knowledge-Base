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

namespace app\common\service\ai;

use app\common\cache\KeyPoolCache;
use app\common\enum\ChatEnum;
use app\common\model\chat\Models;
use Exception;
use Firebase\JWT\JWT;

/**
 * AI向量化训练服务类
 */
class VectorService extends VectorBaseService
{
    private int $mainModelId;
    private array $configs;

    public function __construct(int $mainModelId)
    {
        $this->mainModelId = $mainModelId;
        $this->configs = [];

        $mainModel = (new Models())->where(['id'=>$this->mainModelId])->findOrEmpty()->toArray();
        $this->configs = json_decode($mainModel['configs'], true);
    }

    /**
     * @notes 转向量
     * @param string $channel (模型通道)
     * @param string $model (模型名称)
     * @param string $document (要转向量的内容)
     * @param bool $isReturnStr (是否以字符串返回向量)
     * @return array|string
     * @throws Exception
     */
    public function toEmbedding(string $channel, string $model, string $document, bool $isReturnStr=false): array|string
    {
        $this->document = $document;

        return match (strtolower($channel)) {
            'openai' => $this->textOpenAi($model, $document, $isReturnStr),
            'zhipu'  => $this->textZhiPu($document, $isReturnStr),
            'xunfei' => $this->textXunFei($document, $isReturnStr),
            'qwen'   => $this->textQwen($model, $document, $isReturnStr),
            'm3e'    => $this->textM3e($document, $isReturnStr),
            default  => [],
        };
    }

    /**
     * @notes OpenAI转向量
     * @param string $model
     * @param string $document
     * @param bool $isReturnStr
     * @return bool|array|string
     * @throws Exception
     * @author fzr
     */
    public function textOpenAi(string $model, string $document, bool $isReturnStr=false): bool|array|string
    {
        $aiType = 'openai';
        $apiBase = 'https://api.openai.com';
        $keyPoolCache = new KeyPoolCache($this->mainModelId, ChatEnum::MODEL_TYPE_EMB, $aiType);

        try {
            // 代理地址
            if (!empty($this->configs['agency_api'])) {
                $apiBase = $this->configs['agency_api'];
            }

            // 验证密钥
            $apiAiKey = $keyPoolCache->getKey();
            if (!$apiAiKey) {
                throw new Exception('请管理员配置向量密钥: ' . $model);
            }

            // 请求参数
            $header[] = 'Authorization: Bearer ' . $apiAiKey;
            // $apiBase .= '/v1/engines/'.$model.'/embeddings';
            $apiBase .= '/v1/embeddings';
            $reqResults = VectorService::curlPost($apiBase, ['model'=>$model, 'input' => [$document], 'encoding_format' => 'base64'], $header);
            $results = json_decode($reqResults, true);

            if (!empty($results['error'])) {
                $keyPoolCache->takeDownKey($results['error']['message'], $apiBase);
                throw new Exception($results['error']['message']);
            }

            if (empty($results['data'][0]['embedding']) and !empty($results['detail'])) {
                $keyPoolCache->takeDownKey($results['error']['message'], $apiBase);
                throw new Exception($results['detail']);
            }

            // 提取内容
            $base64 = $results['data'][0]['embedding'] ?? '';
            if (!$base64) {
                throw new Exception('解析问题失败了!');
            }

            $this->usage = $results['usage'];

            // 数组长度: 1536
            if (is_string($base64)) {
                $embedding = base64_decode($base64);
                $floatArray = unpack('f*', $embedding);
                $embArray = array_values($floatArray);
            } else {
                $embArray = $base64;
            }

            // 返回字符串
            if (!$isReturnStr) {
                return $embArray;
            }

            return '[' . implode(',', $embArray) . ']';
        } catch (Exception $e) {
            $error = $keyPoolCache->takeDownKey($e->getMessage(), $apiBase);
            throw new Exception($error);
        }
    }

    /**
     * @notes 智普转向量
     * @param string $document
     * @param bool $isReturnStr
     * @return string|array
     * @throws Exception
     * @author fzr
     */
    public function textZhiPu(string $document, bool $isReturnStr=false): string|array
    {
        $aiType  = 'zhipu';
        $apiBase = 'https://open.bigmodel.cn';
        $keyPoolCache = new KeyPoolCache($this->mainModelId, ChatEnum::MODEL_TYPE_EMB);
        try {
            // 代理地址
            if (!empty($this->configs['agency_api'])) {
                $apiBase = $this->configs['agency_api'];
            }

            // 请求地址
            $apiBase .= '/api/paas/v3/model-api/text_embedding/invoke';

            // 验证密钥
            $apiAiKey = $keyPoolCache->getKey();
            if (!$apiAiKey) {
                throw new Exception('请管理员配置向量密钥: ' . $aiType);
            }

            // 生成令牌
            $expireTime = 86400 * 7;
            $keyArray = explode('.', $apiAiKey);
            if (count($keyArray) < 2) {
                $keyPoolCache->takeDownKey('Invalid Authorization code', $apiBase);
                throw new Exception('配置的Key密钥不正确');
            }
            [$id, $secret] = explode('.', $apiAiKey);
            $nowMs = round(microtime(true) * 1000);
            $payload = [
                'api_key'   => $id,
                'exp'       => ($nowMs + ($expireTime * 1000)),
                'timestamp' => $nowMs
            ];
            $token = JWT::encode($payload, $secret, 'HS256',null, ['sign_type'=> 'SIGN']);

            // 请求参数
            $header[] = 'Authorization: ' . $token;

            // 发起请求
            $reqResults = self::curlPost($apiBase, ['prompt'=>$document, 'model'=>'text_embedding'], $header);
            $reqResults = json_decode($reqResults, true);
            if ($reqResults['code'] !== 200) {
                $keyPoolCache->takeDownKey($reqResults['msg'], $apiBase);
                throw new Exception($reqResults['msg']);
            }

            $this->usage = $reqResults['data']['usage'];

            // 数组长度: 1024
            $embArray = $reqResults['data']['embedding'];

            // 扩充到1536维度
            $embArray = $this->expandFeatures($embArray, 1536);
            if (!$isReturnStr) {
                return $embArray;
            }

            return '[' . implode(',', $embArray) . ']';
        } catch (Exception $e) {
            $keyPoolCache->takeDownKey($e->getMessage(), $apiBase);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 讯飞转向量
     * @param string $document
     * @param bool $isReturnStr
     * @return string|array
     * @throws Exception
     * @author fzr
     */
    public function textXunFei(string $document, bool $isReturnStr=false): string|array
    {
        $aiType = 'xunfei';
        $apiBase = 'https://emb-cn-huabei-1.xf-yun.com';
        $keyPoolCache = new KeyPoolCache($this->mainModelId, ChatEnum::MODEL_TYPE_EMB, $aiType);
        try {
            // 代理地址
            if (!empty($this->configs['agency_api'])) {
                $apiBase = $this->configs['agency_api'];
            }

            // 请求地址
            $apiBase .= '/';

            // 验证密钥
            $apiAiKey = $keyPoolCache->getKey();
            if (!$apiAiKey) {
                throw new Exception('请管理员配置向量密钥: ' . $aiType);
            }

            // 基础配置
            $parseUrl  = parse_url($apiBase);
            $appId     = $apiAiKey['appid'];
            $apikey    = $apiAiKey['key'];
            $apiSecret = $apiAiKey['secret'];
            $method    = 'POST';

            // 参与签名的字段 host, date, request-line
            $timestamp     = time();
            $rfc1123Format = gmdate("D, d M Y H:i:s \G\M\T", $timestamp);

            // 签名字符串构建
            $signString = ["host: " . $parseUrl["host"], "date: " . $rfc1123Format, $method . " " . $parseUrl["path"] . " HTTP/1.1"];
            $sign = implode("\n", $signString);

            // 对签名字符串进行HMAC-SHA256加密，得到签名结果
            $sha = hash_hmac('sha256', $sign, $apiSecret,true);
            $signatureShaBase64 = base64_encode($sha);

            // 将API密钥、算法、头部信息和签名结果拼接成一个授权URL
            $authUrl = "api_key=\"$apikey\", algorithm=\"hmac-sha256\", headers=\"host date request-line\", signature=\"$signatureShaBase64\"";

            // 对授权URL进行Base64编码，并添加到原始地址后面作为查询参数
            $url = $apiBase . '?' . http_build_query([
                    'host' => $parseUrl['host'],
                    'date' => $rfc1123Format,
                    'authorization' => base64_encode($authUrl),
                ]);

            $jsonString    = json_encode([['role'=>'user', 'content'=>$document]]);
            $utf8Bytes     = mb_convert_encoding($jsonString, 'UTF-8');
            $base64Encoded = base64_encode($utf8Bytes);

            $paramDict = array(
                'header' => array(
                    'app_id' => $appId,
                    'status' => 3
                ),
                'parameter' => array(
                    'emb' => [
                        'domain' => 'query',
                        'feature' => [
                            'encoding' => 'utf8'
                        ]
                    ]
                ),
                'payload' => array(
                    'messages' => [
                        'text' => $base64Encoded
                    ]
                )
            );

            $reqResults = self::curlPost($url, $paramDict);
            if (!$reqResults) {
                throw new Exception('向量词汇分析失败!');
            }

            $results = json_decode($reqResults, true);
            if ($results['header']['code']) {
                $err = $results['header']['message'];
                $keyPoolCache->takeDownKey($err, $apiBase);
                if ($err === 'licc failed') {
                    throw new Exception($err . ': Key不具备调用权限,请联系讯飞工作人员开通~');
                }
                throw new Exception($results['header']['message']);
            }

            $base64Emb = $results['payload']['feature']['text']??'';
            if (!$base64Emb) {
                throw new Exception('向量解析失败!');
            }

            $embeddings = base64_decode($base64Emb);
            $floatArray = unpack('f*', $embeddings);
            $embArray   = array_values($floatArray);

            // 数组长度 2560
            if (!$isReturnStr) {
                return $embArray;
            }

            return '[' . implode(',', $embArray) . ']';
        } catch (Exception $e) {
            $keyPoolCache->takeDownKey($e->getMessage(), $apiBase);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 通义千问转向量
     * @param string $model
     * @param string $document
     * @param bool $isReturnStr
     * @return mixed|string
     * @throws Exception
     */
    public function textQwen(string $model, string $document, bool $isReturnStr=false): string|array
    {
        $aiType = 'qwen';
        $apiBase = 'https://dashscope.aliyuncs.com';
        $keyPoolCache = new KeyPoolCache($this->mainModelId, ChatEnum::MODEL_TYPE_EMB, $aiType);

        try {
            // 代理地址
            if (!empty($this->configs['agency_api'])) {
                $apiBase = $this->configs['agency_api'];
            }

            // 验证密钥
            $apiAiKey = $keyPoolCache->getKey();
            if (!$apiAiKey) {
                throw new Exception('请管理员配置向量密钥: ' . $model);
            }

            // 请求参数
            $header[] = 'Authorization: Bearer ' . $apiAiKey;
            $apiBase .= '/api/v1/services/embeddings/text-embedding/text-embedding';
            $reqResults = VectorService::curlPost($apiBase, [
                'model'=>$model,
                'input' => ['texts'=>[$document]],
                'parameters' => [
                    'text_type' => 'query'
                ]
            ], $header);

            $results = json_decode($reqResults, true);

            if (!empty($results['error'])) {
                $keyPoolCache->takeDownKey($results['error']['message'], $apiBase);
                throw new Exception($results['error']['message']);
            }

            if (empty($results['output']['embeddings'][0]['embedding'])) {
                $keyPoolCache->takeDownKey($results['message'], $apiBase);
                throw new Exception($results['message']);
            }

            // 提取内容 (维度 1536)
            $embArray = $results['output']['embeddings'][0]['embedding'];

            // 返回字符串
            if (!$isReturnStr) {
                return $embArray;
            }

            return '[' . implode(',', $embArray) . ']';
        } catch (Exception $e) {
            $error = $keyPoolCache->takeDownKey($e->getMessage(), $apiBase);
            throw new Exception($error);
        }
    }

    /**
     * @notes M3e (废弃,逻辑和openai相同)
     * @param string $document
     * @param bool $isReturnStr
     * @return string|array
     * @throws Exception
     * @author fzr
     */
    public function textM3e(string $document, bool $isReturnStr=false): string|array
    {
        // 基础配置
        $aiType = 'm3e';
        $apiBase = '';
        $keyPoolCache = new KeyPoolCache($this->mainModelId, ChatEnum::MODEL_TYPE_EMB, $aiType);
        try {
            // 代理地址
            if (!empty($this->configs['agency_api'])) {
                $apiBase = $this->configs['agency_api'];
            }

            // 验证地址
            if (!$apiBase) {
                throw new Exception('请配置向量模型请求地址');
            }

            // 验证密钥
            $apiAiKey = $keyPoolCache->getKey();
            if (!$apiAiKey) {
                throw new Exception('请管理员配置向量密钥: ' . $aiType);
            }

            // 请求参数
            $header[] = 'Authorization: Bearer ' . $apiAiKey;
            $apiBase .= '/v1/embeddings';

            $reqResults = self::curlPost($apiBase, ['model'=>'m3e', 'input'=> [$document]], $header);
            $results = json_decode($reqResults, true);
            if (!empty($results['detail'])) {
                $keyPoolCache->takeDownKey($results['detail'], $apiBase);
                throw new Exception($results['detail']);
            }

            if (!empty($results['error'])) {
                throw new Exception($results['error']['message']);
            }

            // 数组长度: 1536
            $embArray = $results['data'][0]['embedding']??'';
            if (!$embArray) {
                throw new Exception('向量解析失败了!');
            }

            $this->usage = $results['usage'];
            if (!$isReturnStr) {
                return $embArray;
            }

            return '[' . implode(',', $embArray) . ']';
        } catch (Exception $e) {
            $keyPoolCache->takeDownKey($e->getMessage(), $apiBase);
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 发起POST请求
     * @param string $url
     * @param array $data
     * @param array $header
     * @return bool|string
     * @throws @Exception
     * @author fzr
     */
    public static function curlPost(string $url, array $data, array $header = []): bool|string
    {
        $headers  = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        $headers = array_merge($headers, $header);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // 执行CURL请求
        $response = curl_exec($curl);

        // 检查是否有错误发生
        if (curl_errno($curl)) {
            $error = curl_error($curl);
            throw new Exception($error);
        }

        // 关闭 cURL 句柄
        curl_close($curl);
        return $response;
    }

    /**
     * @notes 获取计费信息
     * @return array
     * @author fzr
     */
    public function getUsage(): array
    {
        if (!$this->usage) {
            $tokens = gpt_tokenizer_count($this->document);
            $this->usage = [
                'prompt_tokens' => $tokens,
                'total_tokens'  => $tokens
            ];
        }

        return  [
            'prompt_tokens'     => $this->usage['prompt_tokens'],
            'total_tokens'      => $this->usage['total_tokens'],
            'completion_tokens' => $this->usage['total_tokens'] - $this->usage['prompt_tokens'],
            'str_length'        => mb_strlen($this->document)
        ]??[];
    }
}