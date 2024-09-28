<?php

namespace app\common\service\draw\engine;

use app\common\enum\draw\DrawEnum;
use app\common\service\ConfigService;
use Exception;
use WpOrg\Requests\Requests;

/**
 * Class DrawSD
 * @package app\common\service\draw\engine
 */
class DrawSd extends DrawBase implements DrawInterface
{
    public function __construct(string $apiUrl = '')
    {
        $this->baseUrl = $apiUrl;
        $this->headers = [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @notes 绘画
     * @param array $params
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2023/11/30 10:50
     */
    public function imagine(array $params): array
    {
        $data = [
            'prompt' => $params['prompt'],
            'negative_prompt' => $params['negative_prompt'],
            'seed' => intval($params['seed']) < 1 ? -1 : intval($params['seed']),
            'sampler_name' => $params['sampler_name'],
            'steps' => $params['steps'],
            'cfg_scale' => $params['cfg_scale'],
            'batch_size' => 1,
            'width' => $params['width'],
            'height' => $params['height'],
        ];

        // 提示词并入lora
        foreach ($params['loras'] as $lora) {
            $params['prompt'] .= ", <lora:$lora:1>";
        }

        if (!empty($params['engine'])) {
            $data['override_settings']['sd_model_checkpoint'] = $params['engine'];
        }

        // 文生图
        $url = $this->baseUrl . '/sdapi/v1/txt2img';
        if (!empty($params['image'])) {
            // 图生图
            $url = $this->baseUrl . '/sdapi/v1/img2img';
            $data['init_images'][] = $params['image'];
        }
        $options = [
            'timeout' => 600
        ];
        $response = Requests::post($url, $this->headers, json_encode($data), $options);
        return $this->getResponseData($response);
    }

    public function imagineUv(array $params)
    {
    }


    /**
     * @notes 获取模型
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/1/4 11:19
     */
    public function getModel(): array
    {
        $url = $this->baseUrl . '/sdapi/v1/sd-models';
        $response = Requests::get($url);
        return $this->getResponseData($response);
    }

    /**
     * @notes 获取微调模型
     * @return array
     * @throws Exception
     * @author JXDN
     * @date 2024/5/15 14:43
     */
    public function getLoras(): array
    {

        $url = $this->baseUrl . '/sdapi/v1/loras';
        $response = Requests::get($url);
        return $this->getResponseData($response);
    }

    /**
     * @notes 获取采样算法
     * @return array
     * @throws Exception
     * @author JXDN
     * @date 2024/5/15 14:43
     */
    public function getSamplers(): array
    {

        $url = $this->baseUrl . '/sdapi/v1/samplers';
        $response = Requests::get($url);
        return $this->getResponseData($response);
    }

    /**
     * @notes 请求结果
     * @param $response
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2023/11/30 10:50
     */
    public function getResponseData($response): array
    {
        $responseData = json_decode($response->body, true);
        if (!empty($responseData['detail'][0]['msg'])) {
            throw new Exception($responseData['detail'][0]['msg']);
        }
        return $responseData;
    }
}