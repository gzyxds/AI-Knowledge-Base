<?php

namespace app\queue;

use app\api\logic\draw\DrawLogic;
use app\common\enum\draw\DrawEnum;
use app\common\model\draw\DrawRecords;
use app\common\service\draw\DrawDriver;
use app\common\service\FileService;
use think\facade\Log;
use think\queue\Job;

class SdQueueJob
{
    public function fire(Job $job, array $data): void
    {
        $attemptsNums = $job->attempts();
        $recordsModel = new DrawRecords();
        $task = $recordsModel->findOrEmpty($data['record_id']);
        try {
            echo "\n\n";
            echo "[". date('Y-m-d H:i:s') ."] 执行的任务: 尝试次数:$attemptsNums @taskId:" . $data['record_id'] . "\n";

            if ($job->attempts() > 3) {
                echo "尝试次数大于3次，任务失败@taskId: " . $data['record_id'] . " \n";
                throw new \Exception('尝试次数大于3次，任务失败');
            }

            // 更新任务状态
            if ($task->isEmpty()) {
                echo "任务不存在@taskId: " . $data['record_id'] . " \n";
                throw new \Exception('任务不存在');
            }

            if ($task->model != DrawEnum::API_SD) {
                echo "任务非SD绘画模型@taskId: " . $data['record_id'] . " \n";
                throw new \Exception('任务非SD绘画模型');
            }

            // 非待处理状态
            if ($task->status != DrawEnum::STATUS_IN_PROGRESS) {
                echo "任务非待处理状态@taskId: " . $data['record_id'] . " \n";
                throw new \Exception('任务非待处理状态');
            }


            // 提交请求到SD
            $drawEngine = new DrawDriver(DrawEnum::API_SD);
            $requestData = $task->toArray();
            $requestData['complex_params'] = json_decode($requestData['complex_params'], true);

            // 处理上传图片 获取上传图片base64
            $image = '';
            if ($requestData['type'] == DrawEnum::TYPE_IMAGE_TO_IMAGE && !empty($requestData['image_base'])) {
                $image = FileService::getFileUrl($requestData['image_base']);
                $image = imgToBase64($image, false, false);
            }

            // 比例参数处理
            $width = '512';
            $height = '512';
            if (!empty($requestData['scale'])) {
                list($width, $height) = explode('x', $requestData['scale']);
            }

            // 绘画模型
            $engine = '';
            if (!empty($requestData['engine'])) {
                $engine = $requestData['engine'];
            }

            $response = $drawEngine->imagine([
                'prompt'          => $requestData['prompt_desc'],
                'negative_prompt' => $requestData['negative_prompt'],
                'width'           => $width,
                'height'          => $height,
                'engine'          => $engine,
                'image'           => $image,
                'seed'            => $requestData['complex_params']['seed'] ?: -1,
                'sampler_name'    => $requestData['complex_params']['sampler_name'] ?? '',
                'steps'           => $requestData['complex_params']['step'] ?? '',
                'cfg_scale'       => $requestData['complex_params']['cfg_scale'] ?? '',
                'batch_size'      => 1,
                'loras'           => json_decode($requestData['loras'])
            ]);

            $requestData['file_domain'] = $data['file_domain'] ?? '';
            (new DrawLogic($task->user_id))->notifySd($requestData, $response);

        } catch (\Exception $e) {
            echo "出现错误：" . $e->getMessage() . "\n";
            $errMsg = "任务提交失败: " . $task['id'] . ':__@__:' . $e->getMessage();
            Log::error($errMsg);
            // 失败处理
            try {
                (new DrawLogic($task->user_id))->failRecordHandle($task, ['fail_reason' => $e->getMessage()]);
            } catch (\Exception $e) {
                echo "任务失败回调失败: " . $e->getMessage() . "\n";
                $job->delete();
            }
        } finally {
            // 删除任务
            echo "任务已删除@taskId: " . $data['record_id'] . " \n";
            $job->delete();
        }
    }
}