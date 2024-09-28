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

namespace app\adminapi\logic\finance;

use app\common\enum\RefundEnum;
use app\common\logic\BaseLogic;
use app\common\model\member\MemberOrder;
use app\common\model\recharge\RechargeOrder;
use app\common\model\refund\RefundLog;
use app\common\model\refund\RefundRecord;
use Exception;

/**
 * 退款
 */
class RefundLogic extends BaseLogic
{
    /**
     * @notes 退款统计
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/3/3 12:09
     */
    public static function stat(): array
    {
        $records = (new RefundRecord())->select()->toArray();
        $total   = 0;
        $ing     = 0;
        $success = 0;
        $error   = 0;

        foreach ($records as $record) {
            $total += $record['order_amount'];
            switch ($record['refund_status']) {
                case RefundEnum::REFUND_ING:
                    $ing += $record['order_amount'];
                    break;
                case RefundEnum::REFUND_SUCCESS:
                    $success += $record['order_amount'];
                    break;
                case RefundEnum::REFUND_ERROR:
                    $error += $record['order_amount'];
                    break;
            }
        }

        return [
            'total'   => round($total, 2),
            'ing'     => round($ing, 2),
            'success' => round($success, 2),
            'error'   => round($error, 2),
        ]??[];
    }

    /**
     * @notes 退款日志
     * @param $recordId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/3/3 14:25
     */
    public static function refundLog($recordId): array
    {
        return (new RefundLog())
            ->where(['record_id'=>$recordId])
            ->order(['id' => 'desc'])
            ->hidden(['refund_msg'])
            ->append(['handler', 'refund_status_text'])
            ->select()
            ->toArray();
    }

    /**
     * @notes 重新退款
     * @param $recordId
     * @param $adminId
     * @return bool
     * @author 段誉
     * @date 2023/3/1 9:47
     */
    public static function refundAgain($recordId, $adminId): bool
    {
        $modelRefundRecord = new RefundRecord();
        $modelRefundRecord->startTrans();
        try {
            $record = $modelRefundRecord->findOrEmpty($recordId)->toArray();
            $order = [];
            if ($record['order_type'] == 'member') {
                $order = MemberOrder::findOrEmpty($record['order_id']);
            }
            if ($record['order_type'] == 'recharge') {
                $order = RechargeOrder::findOrEmpty($record['order_id']);
            }


            if (empty($order)) {
                throw new Exception('退款订单错误');
            }

            RefundRecord::update([
                'refund_status' => RefundEnum::REFUND_ING
            ],['id'=>$recordId]);

            \app\common\logic\RefundLogic::refund($order, $record['id'], $order['order_amount'], $adminId);

            $modelRefundRecord->commit();
            return true;
        } catch (Exception $e) {
            $modelRefundRecord->rollback();
            self::setError($e->getMessage());
            return false;
        }
    }
}