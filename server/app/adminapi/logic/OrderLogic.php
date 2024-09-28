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

namespace app\adminapi\logic;

use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\RefundEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\logic\RefundLogic;
use app\common\model\Order;
use app\common\model\refund\RefundRecord;
use app\common\model\user\User;
use Exception;

class OrderLogic extends BaseLogic
{
    /**
     * @notes 订单详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $modelOrder = new Order();
        $detail = $modelOrder
            ->field([
                'id,user_id,order_sn,order_terminal,order_status,pay_status,refund_status',
                'pay_way,order_amount,subject,category,label,pay_time,create_time',
            ])
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();

        if (!$detail) {
            return [];
        }

        $modelUser = new User();
        $detail['user'] = $modelUser
            ->field(['id,sn,avatar,nickname,account,mobile'])
            ->where(['id'=>$detail['user_id']])
            ->findOrEmpty()
            ->toArray();

        $detail['pay_time'] = $detail['pay_time'] ? date('Y-m-d H:i:s', $detail['pay_time']) : '-';
        $detail['desc'] = [
            'order_terminal' => PayEnum::getPaySceneDesc($detail['order_terminal']),
            'refund_status'  => OrderEnum::getRefundStatusDesc($detail['refund_status']),
            'order_status'   => OrderEnum::getOrderStatusDesc($detail['order_status']),
            'pay_status'     => PayEnum::getPayStatusDesc($detail['pay_status']),
            'pay_way'        => PayEnum::getPayDesc($detail['pay_way'])
        ];

        unset($detail['pay_way']);
        unset($detail['order_terminal']);
        return $detail;
    }

    /**
     * @notes 发起退款
     * @param int $orderId
     * @param int $adminId
     * @return bool
     * @author fzr
     */
    public static function refund(int $orderId, int $adminId): bool
    {
        $modelOrder = new Order();
        $modelOrder->startTrans();
        try {
            // 查询要被退款的订单
            $order = $modelOrder
                ->field(['id,user_id,order_sn,order_terminal,order_amount,pay_way,pay_status,refund_status,transaction_id'])
                ->where(['id'=>$orderId])
                ->findOrEmpty()
                ->toArray();

            // 校验订单是否存在的
            if (!$order) {
                throw new Exception('订单不存在!');
            }

            // 校验订单是否已支付
            if ($order['pay_status'] != PayEnum::ISPAID) {
                throw new Exception('当前订单不可退款!');
            }

            // 校验订单是否已退款
            if ($order['refund_status'] == YesNoEnum::YES) {
                throw new Exception('订单已发起退款,退款失败请到退款记录重新退款!');
            }

            // 标记退款
            Order::update([
                'id'            => $order['id'],
                'refund_status' => YesNoEnum::YES
            ], ['id'=>$orderId]);

            // 生成退款记录
            $recordSn = generate_sn(RefundRecord::class, 'sn');
            $record = RefundRecord::create([
                'sn'             => $recordSn,
                'user_id'        => $order['user_id'],
                'order_id'       => $order['id'],
                'order_sn'       => $order['order_sn'],
                'order_amount'   => $order['order_amount'],
                'refund_amount'  => $order['order_amount'],
                'transaction_id' => $order['transaction_id'],
                'order_type'     => RefundEnum::ORDER_TYPE_ORDER,
                'refund_type'    => RefundEnum::TYPE_ADMIN,
                'refund_way'     => RefundEnum::getRefundWayByPayWay($order['pay_way']),
            ]);

            // 退款
            $result = RefundLogic::refund($order, intval($record['id']), $order['order_amount'], $adminId);
            if ($result !== true) {
                throw new Exception(RefundLogic::getError());
            }

            $modelOrder->commit();
            return true;
        } catch (Exception $e) {
            $modelOrder->rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 重新退款
     * @param int $recordId
     * @param int $orderId
     * @param int $adminId
     * @return bool
     * @author fzr
     */
    public static function refundAgain(int $recordId, int $orderId, int $adminId): bool
    {
        $modelRefundRecord = new RefundRecord();
        $modelRefundRecord->startTrans();
        try {
            $record = (new RefundRecord())->findOrEmpty($recordId);
            $order  = (new Order())->findOrEmpty($orderId);

            // 发起退款
            $result = RefundLogic::refund($order, $record['id'], $order['order_amount'], $adminId);
            if ($result !== true) {
                throw new Exception(RefundLogic::getError());
            }

            $modelRefundRecord->commit();
            return true;
        } catch (Exception $e) {
            $modelRefundRecord->rollback();
            self::setError($e->getMessage());
            return false;
        }
    }
}