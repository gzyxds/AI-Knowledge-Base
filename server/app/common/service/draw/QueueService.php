<?php

namespace app\common\service\draw;

use app\api\logic\draw\DrawLogic;
use app\common\model\draw\DrawRecords;
use app\queue\DalleQueueJob;
use app\queue\SdQueueJob;

/**
 * 队列服务类
 */
class QueueService
{
    private static string $SD_JOB = 'ChatMoneySdJob'; // sd绘画任务
    private static string $DALLE_JOB = 'ChatMoneyDalleJob'; // dalle3绘画任务

    /**
     * @notes SD
     * @param array $data
     * @param int $delay
     * @return void
     * @throws \Exception
     * @author JXDN
     * @date 2024/05/30 17:25
     */
    public static function pushSd(array $data, int $delay = 0): void
    {
        try {
            queue(SdQueueJob::class, $data, $delay, self::$SD_JOB);
        } catch (\Exception $e) {
            $recordsModel = new DrawRecords();
            $task = $recordsModel->findOrEmpty($data['record_id']);
            (new DrawLogic($task->user_id))->failRecordHandle($task, ['fail_reason' => $e->getMessage()]);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @notes dalle3
     * @param array $data
     * @param int $delay
     * @author mjf
     * @date 2024/8/2 17:46
     */
    public static function pushDalle(array $data, int $delay = 0): void
    {
        queue(DalleQueueJob::class, $data, $delay, self::$DALLE_JOB);
    }

}