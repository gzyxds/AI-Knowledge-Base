<?php

namespace app\common\service\draw\engine;

use app\common\cache\KeyPoolCache;
use app\common\enum\draw\DrawEnum;
use app\common\enum\PoolEnum;
use WpOrg\Requests\Requests;
use Exception;

/**
 * Class DrawDalle3
 * @package app\common\service\draw\engine
 */
class DrawDalle3 extends DrawBase implements DrawInterface
{
    protected string $apiToken;

    /** 初始化
     * @param string $apiUrl
     * @throws Exception
     */
    public function __construct(string $apiUrl = '')
    {
        $keyPoolServer = (new KeyPoolCache(DrawEnum::API_DALLE3, PoolEnum::TYPE_DRAW));
        $this->apiToken = $keyPoolServer->getKey();

        if (empty($this->apiToken)) {
            throw new Exception('请在后台配置key');
        }

        $this->baseUrl = 'https://api.openai.com';
        if (!empty($apiUrl)) {
            $this->baseUrl = $apiUrl;
        }
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiToken,
        ];
    }

    /**
     * @notes 绘画
     * @param array $params
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/11/30 10:50
     */
    public function imagine(array $params)
    {
        $url = $this->baseUrl . '/v1/images/generations';
        $data = json_encode([
            'model' => 'dall-e-3',
            'prompt' => $params['prompt'],
            'n' => 1,
            'size' => $params['size'],
            'response_format' => 'b64_json',
            'quality' => $params['quality'] ?? 'standard',
            'style' => $params['style'] ?? 'vivid',
        ]);
        $options['timeout'] = 60;
        $response = Requests::post($url, $this->headers, $data, $options);
        return $this->getResponseData($response);
    }

    public function imagineUv(array $params)
    {
    }

    /**
     * @notes 请求结果
     * @param $response
     * @return array
     * @throws \Exception
     * @author mjf
     * @date 2023/11/30 10:50
     */
    public function getResponseData($response)
    {
        $responseData = json_decode($response->body, true);
        if (isset($responseData['error'])) {
            throw new Exception($responseData['error']['message']);
        }
        return $responseData;
    }
}