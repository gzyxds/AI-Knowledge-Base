<?php

namespace app\queue;

use app\api\logic\draw\DrawLogic;
use app\common\enum\draw\DrawEnum;
use app\common\model\draw\DrawRecords;
use app\common\service\draw\DrawDriver;
use think\facade\Log;
use think\queue\Job;
use Exception;

class DalleQueueJob
{
    public function fire(Job $job, array $data)
    {
        $attemptsNums = $job->attempts();
        // 更新任务状态
        $recordModel = new DrawRecords();
        $task        = $recordModel->findOrEmpty($data['record_id']);

        try {
            echo "\n\n";
            echo "执行的dalle3任务: RT:$attemptsNums@recordId:" . $data['record_id'] . "\n";

            if ($task->isEmpty()) {
                echo "任务不存在: " . $data['id'] . " \n";
                $job->delete();
                throw new Exception('任务不存在');
            }

            if ($task->model != DrawEnum::API_DALLE3) {
                echo "任务非Dalle3绘画模型: " . $data['id'] . " \n";
                $job->delete();
                throw new Exception('绘画模型异常');
            }

            // 非待处理状态
            if ($task->status != DrawEnum::STATUS_IN_PROGRESS) {
                echo "任务非待处理状态: " . $data['id'] . " \n";
                $job->delete();
                throw new Exception('绘画状态异常');
            }

            if ($job->attempts() > 3) {
                echo "尝试次数大于3次: " . $data['record_id'] . " \n";
                throw new \Exception('尝试次数大于3次，任务失败');
            }

            // 提交请求到意间
            $drawEngine  = new DrawDriver(DrawEnum::API_DALLE3);
            $requestData = $task->toArray();
            $response    = $drawEngine->imagine([
                'prompt'  => $requestData['prompt_desc'],
                'size'    => $requestData['scale'],
                'quality' => $requestData['quality'],
                'style'   => $requestData['style'],
            ]);

            $requestData['file_domain'] = $data['file_domain'] ?? '';

            // 处理绘画结果
            (new DrawLogic($task->user_id, ['draw_api' => $task->model]))->notifyDalle3($requestData, $response);

        } catch (\Throwable $e) {
            $errMsg = "dalle3任务提交失败: " . $task['id'] . ':__@__:' . $e->getMessage();
            Log::error($errMsg);
            echo $errMsg . "\n";

            // 失败处理
            try {
                (new DrawLogic($task->user_id, ['draw_api' => DrawEnum::API_DALLE3]))->failRecordHandle($task, ['fail_reason' => $e->getMessage()]);
            } catch (\Exception $e) {
                echo "dalle3任务失败,更新状态失败: " . $e->getMessage() . "\n";
                $job->delete();
            }
        } finally {
            // 删除任务
            $job->delete();
        }
    }


}