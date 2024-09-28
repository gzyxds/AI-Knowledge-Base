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

namespace app\common\command;

use app\api\logic\draw\DrawLogic;
use app\common\enum\draw\DrawEnum;
use app\common\model\draw\DrawRecords;
use app\common\service\ConfigService;
use app\common\service\draw\engine\DrawMj;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

/**
 * 绘画失败处理
 * Class DrawFail
 * @package app\common\command
 */
class DrawFail extends Command
{
    protected function configure()
    {
        $this->setName('draw_fail')
            ->setDescription('处理生成超时的绘画记录');
    }

    protected function execute(Input $input, Output $output): bool
    {
        $recordModel = new DrawRecords();
        $records     = $recordModel->where(['status' => DrawEnum::STATUS_IN_PROGRESS])
            ->select()
            ->toArray();

        if (empty($records)) {
            return true;
        }

        $nowTime = time();
        foreach ($records as $record) {
            $defaultConfig = DrawEnum::getDrawDefaultConfig($record['model']);
            $drawConfig    = ConfigService::get('draw_config', $record['model'], $defaultConfig);
            $expireTime    = ($drawConfig['time_out'] ?? 10) * 60;

            $createTime = strtotime($record['create_time']);
            if ($createTime + $expireTime > $nowTime) {
                continue;
            }

            $failReason = '任务响应失败';

            if ($record['model'] == DrawEnum::API_MJ_GOAPI) {
                $flag = $this->mjCheck($record);
                if ($flag === true) {
                    continue;
                }
                if (!empty($flag)) {
                    $failReason .= '(' . $flag . ')';
                }
            }

            $drawLogic = new DrawLogic($record['user_id'], ['draw_api' => $record['model']]);
            $drawLogic->failRecordHandle($record, ['fail_reason' => $failReason]);
        }

        return true;
    }


    /**
     * @notes mj任务查询
     * @param $record
     * @return bool|string
     * @author mjf
     * @date 2024/8/15 15:17
     */
    private function mjCheck($record): bool|string
    {
        try {
            if (empty($record['task_id'])) {
                return false;
            }

            $service   = new DrawMj();
            $response  = $service->fetch($record['task_id']);
            $drawLogic = new DrawLogic($record['user_id'], ['draw_api' => $record['model']]);
            return $drawLogic->notifyMj($response);

        } catch (\Exception $e) {
            Log::write('mj查询任务失败' . $e->getMessage());
            return $e->getMessage();
        }
    }

}