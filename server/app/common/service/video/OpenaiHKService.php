<?php

namespace app\common\service\video;

use app\common\cache\KeyPoolCache;
use app\common\enum\PoolEnum;
use app\common\enum\VideoEnum;
use WpOrg\Requests\Requests;
use Exception;

/**
 * openai-hk服务类
 */
class OpenaiHKService
{
    protected string $baseUrl = 'https://api.openai-hk.com';

    protected array $headers = [];

    protected array $options = [];

    /**
     * @throws Exception
     */
    public function __construct(string $version = 'pro')
    {
        $keyPoolServer = (new KeyPoolCache(VideoEnum::OPENAIHK, PoolEnum::TYPE_VIDEO));
        $key = $keyPoolServer->getKey();
        if (empty($key)) {
            throw new Exception('请在后台配置key');
        }

        if ($version == 'pro') {
            $this->baseUrl .= '/pro';
        }

        $this->headers = [
            'Authorization' => 'Bearer ' . $key,
            'Content-Type' => 'application/json'
        ];

        $this->options = [
            'verify' => false,
            'timeout' => 100,
        ];
    }

    /**
     * @notes 提交音乐
     * @param array $params
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/5/28 16:27
     */
    public function video(array $params): array
    {
        $data = [
            "aspect_ratio" => $params['aspect_ratio'] ?? '',
            "expand_prompt" => false,
            "image_url" => $params['image_url'] ?? '',
            "user_prompt" => $params['user_prompt']
        ];
        $url = $this->baseUrl . '/luma/generations';
        $response = Requests::post($url, $this->headers, json_encode($data), $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes 详情
     * @param string $taskId
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/5/28 16:27
     */
    public function query(string $taskId): array
    {
        $url = $this->baseUrl . '/luma/generations/' . $taskId;
        $response = Requests::get($url, $this->headers, $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes 响应处理
     * @param $response
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/5/28 16:24
     */
    public function getResponseData($response): array
    {
        $responseData = json_decode($response->body, true);

        if (!empty($responseData['error'])) {
            $error = $responseData['error']['message'] ?? '系统错误';
            throw new Exception($error);
        }

        return $responseData;
    }

}