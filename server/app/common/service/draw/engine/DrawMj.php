<?php

namespace app\common\service\draw\engine;

use app\common\cache\KeyPoolCache;
use app\common\enum\draw\DrawEnum;
use app\common\enum\draw\DrawTaskEnum;
use app\common\enum\PoolEnum;
use app\common\service\ConfigService;
use WpOrg\Requests\Requests;
use Exception;

/**
 * Class DrawGoApi
 * @package app\common\service\draw\engine
 */
class DrawMj extends DrawBase implements DrawInterface
{
    protected string $apiToken;

    protected array $options;

    protected string $processMode;

    /** 初始化
     * @throws Exception
     */
    public function __construct()
    {
        $keyPoolServer  = (new KeyPoolCache(DrawEnum::API_MJ_GOAPI, PoolEnum::TYPE_DRAW));
        $this->apiToken = $keyPoolServer->getKey();
        if (empty($this->apiToken)) {
            throw new Exception('请在后台配置key');
        }

        $this->baseUrl    = 'https://api.midjourneyapi.xyz';
        $this->notifyHook = request()->domain() . '/api/draw.draw/notifyMj';

        $this->headers = [
            'Content-Type' => 'application/json',
            'X-API-KEY'    => $this->apiToken,
        ];

        $this->options = [
            'verify'  => false,
            'timeout' => 100,
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
        $action = $params['action'] ?? DrawTaskEnum::ACTION_GENERATE;

        return match ($action) {
            DrawTaskEnum::ACTION_GENERATE => $this->generate($params),
            DrawTaskEnum::ACTION_PAN_DOWN,
            DrawTaskEnum::ACTION_PAN_UP,
            DrawTaskEnum::ACTION_PAN_LEFT,
            DrawTaskEnum::ACTION_PAN_RIGHT => $this->pan($params),
            DrawTaskEnum::ACTION_ZOOM_OUT_2X,
            DrawTaskEnum::ACTION_ZOOM_OUT_1_5X => $this->outPaint($params),
            DrawTaskEnum::ACTION_VARIATION,
            DrawTaskEnum::ACTION_LOW_VARIATION,
            DrawTaskEnum::ACTION_HIGH_VARIATION => $this->variation($params),
            DrawTaskEnum::ACTION_UPSCALE,
            DrawTaskEnum::ACTION_UPSCALE_2X,
            DrawTaskEnum::ACTION_UPSCALE_4X,
            DrawTaskEnum::ACTION_REDO_UPSCALE_SUBTLE,
            DrawTaskEnum::ACTION_REDO_UPSCALE_CREATIVE,
            DrawTaskEnum::ACTION_UPSCALE_SUBTLE,
            DrawTaskEnum::ACTION_UPSCALE_CREATIVE => $this->upscale($params),
            DrawTaskEnum::ACTION_REROLL => $this->Reroll($params),
            DrawTaskEnum::ACTION_INPAINT => $this->inPaint($params),
            default => throw new Exception('操作异常!'),
        };
    }

    public function imagineUv(array $params)
    {
    }

    /**
     * @notes 图片生成
     * @param array $params
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/8/14 10:54
     */
    public function generate(array $params): array
    {
        $defaultConfig     = DrawEnum::getDrawDefaultConfig(DrawEnum::API_MJ_GOAPI);
        $drawConfig        = ConfigService::get('draw_config', DrawEnum::API_MJ_GOAPI, $defaultConfig);
        $this->processMode = !empty($drawConfig['process_mode']) ? $drawConfig['process_mode'] : DrawTaskEnum::MODE_FAST;

        $url      = $this->baseUrl . '/mj/v2/imagine';
        $data     = json_encode([
            "prompt"           => $params['prompt'],
            "aspect_ratio"     => $params['scale'] ?? '1:1',
            "process_mode"     => $this->processMode,
            "webhook_endpoint" => $this->notifyHook,
            "webhook_secret"   => ''
        ]);
        $response = Requests::post($url, $this->headers, $data, $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes Reroll
     * @param array $params
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/8/14 10:58
     */
    public function Reroll(array $params): array
    {
        $url  = $this->baseUrl . '/mj/v2/reroll';
        $data = json_encode([
            "origin_task_id"   => $params['origin_task_id'],
            "prompt"           => $params['prompt'] ?? '',
            "aspect_ratio"     => $params['scale'] ?? '',
            "webhook_endpoint" => $this->notifyHook,
            "webhook_secret"   => ''
        ]);

        $response = Requests::post($url, $this->headers, $data, $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes upscale
     * @param array $params
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/8/14 11:02
     */
    public function upscale(array $params): array
    {
        $index = match ($params['action']) {
            DrawTaskEnum::ACTION_UPSCALE_SUBTLE,
            DrawTaskEnum::ACTION_REDO_UPSCALE_SUBTLE => 'subtle',
            DrawTaskEnum::ACTION_UPSCALE_CREATIVE,
            DrawTaskEnum::ACTION_REDO_UPSCALE_CREATIVE => 'creative',
            DrawTaskEnum::ACTION_UPSCALE_2X => '2x',
            DrawTaskEnum::ACTION_UPSCALE_4X => '4x',
            default => '',
        };

        if (empty($index)) {
            $index = $params['index'] ?? '1';
        }

        $url  = $this->baseUrl . '/mj/v2/upscale';
        $data = json_encode([
            "origin_task_id"   => $params['origin_task_id'],
            "index"            => $index,
            "webhook_endpoint" => $this->notifyHook,
            "webhook_secret"   => ''
        ]);

        $response = Requests::post($url, $this->headers, $data, $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes variation
     * @param array $params
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/8/14 11:07
     */
    public function variation(array $params): array
    {
        $index = match ($params['action']) {
            DrawTaskEnum::ACTION_LOW_VARIATION => 'low_variation',
            DrawTaskEnum::ACTION_HIGH_VARIATION => 'high_variation',
            default => '',
        };

        if (empty($index)) {
            $index = $params['index'] ?? '1';
        }

        $url  = $this->baseUrl . '/mj/v2/variation';
        $data = json_encode([
            "origin_task_id"   => $params['origin_task_id'],
            "index"            => $index,
            "webhook_endpoint" => $this->notifyHook,
            "webhook_secret"   => ''
        ]);

        $response = Requests::post($url, $this->headers, $data, $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes inPaint
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/8/14 11:07
     */
    public function inPaint($params): array
    {
        $url  = $this->baseUrl . '/mj/v2/inpaint';
        $data = json_encode([
            "origin_task_id"   => $params['origin_task_id'],
            "prompt"           => $params['prompt'] ?? '',
            "mask"             => $params['mask'],
            "webhook_endpoint" => $this->notifyHook,
            "webhook_secret"   => ''
        ]);

        $response = Requests::post($url, $this->headers, $data, $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes outPaint
     * @param array $params
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/8/14 11:14
     */
    public function outPaint(array $params): array
    {
        $zoomRatio = match ($params['action']) {
            DrawTaskEnum::ACTION_ZOOM_OUT_1_5X => '1.5',
            DrawTaskEnum::ACTION_ZOOM_OUT_2X => '2',
        };

        $url  = $this->baseUrl . '/mj/v2/outpaint';
        $data = json_encode([
            "origin_task_id"   => $params['origin_task_id'],
            "zoom_ratio"       => $zoomRatio,
            "aspect_ratio"     => $params['scale'] ?? '',
            "prompt"           => $params['prompt'] ?? '',
            "webhook_endpoint" => $this->notifyHook,
            "webhook_secret"   => ''
        ]);

        $response = Requests::post($url, $this->headers, $data, $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes pan
     * @param array $params
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/8/14 11:19
     */
    public function pan(array $params): array
    {
        $direction = match ($params['action']) {
            DrawTaskEnum::ACTION_PAN_DOWN => 'down',
            DrawTaskEnum::ACTION_PAN_UP => 'up',
            DrawTaskEnum::ACTION_PAN_LEFT => 'left',
            DrawTaskEnum::ACTION_PAN_RIGHT => 'right',
        };

        $url  = $this->baseUrl . '/mj/v2/pan';
        $data = json_encode([
            "origin_task_id"   => $params['origin_task_id'],
            "direction"        => $direction,
            "prompt"           => $params['prompt'],
            "webhook_endpoint" => $this->notifyHook,
            "webhook_secret"   => ''
        ]);

        $response = Requests::post($url, $this->headers, $data, $this->options);
        return $this->getResponseData($response);
    }

    /**
     * @notes 详情查询
     * @param string $taskId
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/8/13 15:35
     */
    public function fetch(string $taskId): array
    {
        $url  = $this->baseUrl . '/mj/v2/fetch';
        $data = json_encode([
            "task_id" => $taskId,
        ]);

        $response = Requests::post($url, $this->headers, $data, $this->options);
        return $this->getResponseData($response);
    }


    /**
     * @notes 请求结果
     * @param $response
     * @return array
     * @throws Exception
     * @author mjf
     * @date 2024/8/13 14:37
     */
    public function getResponseData($response): array
    {
        $responseData = json_decode($response->body, true);

        if ($response->status_code !== 200 || empty($responseData['status'])) {
            $reqError  = !empty($responseData['message']) ? $responseData['message'] : '';
            $taskError = $responseData['task_result']['error_messages'][0] ?? '请求失败';
            $errorMsg  = !empty($reqError) ? $reqError : $taskError;

            if ($errorMsg == 'derive from origin task: parent task is outdated') {
                $errorMsg = '任务已过期,无法进行操作';
            }

            throw new Exception($errorMsg);
        }

        return $responseData;
    }
}