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

namespace app\queue;

use think\facade\Cache;

class BaseQueue
{
    private static string $EM_JOB = 'emJob'; // 向量任务
    private static string $QA_JOB = 'qaJob'; // QA的任务

    /**
     * @notes 向量任务队列推入
     * @param array $data
     * @author fzr
     */
    public static function pushEM(array $data): void
    {
        app('queue')->push('app\queue\EmQueueJob', $data, self::$EM_JOB);
    }

    /**
     * @notes QA拆分队列推入
     * @param array $data
     * @author fzr
     */
    public static function pushQA(array $data): void
    {
        app('queue')->push('app\queue\QaQueueJob', $data, self::$QA_JOB);
    }

    /**
     * @notes 取向量队列长度
     * @return int
     * @author fzr
     */
    public static function getEmbJobLength(): int
    {
        $redis = Cache::store('redis')->handler();
        return $redis->lLen('{queues:'.self::$EM_JOB.'}')??0;
    }

    /**
     * @notes 取QA队列长度
     * @return int
     * @author fzr
     */
    public static function getQaJobLength(): int
    {
        $redis = Cache::store('redis')->handler();
        return $redis->lLen('{queues:'.self::$QA_JOB.'}')??0;
    }

    /**
     * @notes 清空向量队列
     * @return void
     * @author fzr
     */
    public static function clearEmbQueue(): void
    {
        $redis = Cache::store('redis')->handler();
        $redis->del('{queues:'.self::$EM_JOB.'}');
    }

    /**
     * @notes 清空QA队列
     * @return void
     * @author fzr
     */
    public static function clearQaQueue(): void
    {
        $redis = Cache::store('redis')->handler();
        $redis->del('{queues:'.self::$QA_JOB.'}');
    }
}