<?php

namespace app\common\service\music;

use app\common\cache\KeyPoolCache;
use app\common\enum\MusicEnum;
use app\common\enum\PoolEnum;
use WpOrg\Requests\Requests;
use Exception;

/**
 * openai-hk服务类
 */
class OpenaiHKService
{
    protected string $baseUrl = 'https://api.openai-hk.com/sunoapi';

    protected array $headers = [];

    protected array $options = [];

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $keyPoolServer = (new KeyPoolCache(MusicEnum::OPENAIHK, PoolEnum::TYPE_MUSIC));
        $key = $keyPoolServer->getKey();
        if (empty($key)) {
            throw new Exception('请在后台配置key');
        }

        $this->headers = [
            'Authorization' => 'Bearer ' . $key,
            'Content-Type' => 'application/json'
        ];

        $this->options = [
            'verify' => false,
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
    public function music(array $params): array
    {
        if ($params['custom_mode']) {
            $data = [
                "prompt" => $params['prompt'] ?? '',
                "title" => $params['title'],
                "tags" => $params['tags'] ?? '',
                "continue_at" => 0,
                "continue_clip_id" => "",
                'make_instrumental' => (bool)($params['make_instrumental'] ?? 0),
                'mv' => $params['mv'] ?? MusicEnum::CHIRP_V30,
            ];
            $url = $this->baseUrl . '/generate';
        } else {
            $data = [
                'gpt_description_prompt' => $params['prompt'],
                'make_instrumental' => (bool)($params['make_instrumental'] ?? 0),
                'mv' => $params['mv'] ?? MusicEnum::CHIRP_V30,
                "prompt"=> $params['title'],
            ];
            $url = $this->baseUrl . '/generate/description-mode';
        }
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
        $url = $this->baseUrl . '/feed/' . $taskId;
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