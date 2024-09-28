<?php

namespace app\common\service\music;

use app\common\cache\KeyPoolCache;
use app\common\enum\MusicEnum;
use app\common\enum\PoolEnum;
use WpOrg\Requests\Requests;
use Exception;

/**
 * GoAPI服务类
 */
class GoApiService
{
    protected string $baseUrl = 'https://api.goapi.ai';

    protected array $headers = [];

    protected array $options = [];

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $keyPoolServer = (new KeyPoolCache(MusicEnum::GOAPI, PoolEnum::TYPE_MUSIC));
        $key = $keyPoolServer->getKey();
        if (empty($key)) {
            throw new Exception('请在后台配置key');
        }

        $this->headers = [
            'X-API-Key' => $key,
            'Content-Type' => 'application/json'
        ];

        $this->options = [
            'verify' => false,
        ];
    }

    /**
     * @notes 生成音乐
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
                "custom_mode" => True,
                "mv" => $params['mv'] ?? MusicEnum::CHIRP_V30,
                "input" => [
                    "prompt"    => $params['prompt'] ?? '',
                    "title"     => $params['title'],
                    "tags"      => $params['tags'] ?? '',
                    "continue_at"       => 0,
                    "continue_clip_id"  => "",
                    'make_instrumental' => (bool)($params['make_instrumental'] ?? 0),
                ]
            ];
        } else {
            $data = [
                "custom_mode" => False,
                "mv" => $params['mv'] ?? MusicEnum::CHIRP_V30,
                'input' => [
                    'gpt_description_prompt'    => $params['prompt'],
                    'make_instrumental'         => (bool)($params['make_instrumental'] ?? 0),
                ]
            ];
        }

        $url = $this->baseUrl . '/api/suno/v1/music';
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
        $url = $this->baseUrl . '/api/suno/v1/music/' . $taskId;
        $response = Requests::get($url, $this->headers, $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes
     * @param $response
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/5/28 16:24
     */
    public function getResponseData($response): array
    {
        $responseData = json_decode($response->body, true);

        if ($response->status_code !== 200 || empty($responseData['code']) || $responseData['code'] !== 200) {
            $message = !empty($responseData['message']) ? $responseData['message'] : '请求失败';
            throw new Exception($message);
        }

        if (empty($responseData['data'])) {
            throw new Exception("响应异常");
        }

        return $responseData['data'];
    }
}