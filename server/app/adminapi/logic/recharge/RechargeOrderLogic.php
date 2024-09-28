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

namespace app\adminapi\logic\recharge;

use app\common\enum\PayEnum;
use app\common\enum\RefundEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\BaseLogic;
use app\common\logic\RefundLogic;
use app\common\model\recharge\RechargeOrder;
use app\common\model\refund\RefundRecord;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\service\FileService;
use Exception;

/**
 * 充值订单逻辑类
 */
class RechargeOrderLogic extends BaseLogic
{
    /**
     * @notes 充值订单详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $modelRechargeOrder = new RechargeOrder();
        $detail = $modelRechargeOrder
            ->field([
                'id,user_id,order_sn,pay_way,pay_status,refund_status,order_amount',
                'order_terminal,pay_time,create_time,snapshot'
            ])
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();

        if (!$detail) {
            return [];
        }

        $modelUser = new User();
        $user = $modelUser
            ->field(['id,avatar,nickname'])
            ->where(['id'=>$detail['user_id']])
            ->findOrEmpty()
            ->toArray();

        $refund_sn = '';
        if ($detail['refund_status']) {
            $refund_sn = (new RefundRecord())
                    ->where(['user_id'=>$detail['user_id']])
                    ->where(['order_id'=>$detail['id']])
                    ->value('sn')??'';
        }

        $snapshot = json_decode($detail['snapshot'], true);
        $detail['avatar']               = FileService::getFileUrl($user['avatar']);
        $detail['nickname']             = $user['nickname'];
        $detail['name']                 = $snapshot['name']??'';
        $detail['chat_balance']         = $snapshot['chat_balance']??0 ;
        $detail['robot_number']         = $snapshot['robot_number']??0;
        $detail['video_duration']       = $snapshot['video_duration']??0;
        $detail['give_chat_balance']    = $snapshot['give_chat_balance']??0;
        $detail['give_robot_number']    = $snapshot['give_robot_number']??0;
        $detail['give_video_duration']  = $snapshot['give_video_duration']??0;
        $detail['pay_time']             = empty($detail['pay_time']) ? '-' : date('Y-m-d H:i:s', $detail['pay_time']);
        $detail['refund_sn']            = $refund_sn;
        $detail['pay_way_text']         = PayEnum::getPayDesc($detail['pay_way']);
        $detail['pay_status_text']      = PayEnum::getPayStatusDesc($detail['pay_status']);
        $detail['refund_status_text']   = PayEnum::getRefundStatusDesc($detail['refund_status']);
        $detail['order_terminal_text']  = UserTerminalEnum::getTermInalDesc($detail['order_terminal']);
        $detail['order_type_text']      = '充值订单';
        unset($detail['snapshot']);
        return $detail;
    }

    /**
     * @notes 充值订单退款
     * @param int $orderId
     * @param $adminId
     * @return bool|string
     * @author fzr
     */
    public static function refund(int $orderId, $adminId): bool|string
    {
        $modelUser = new User();
        $modelRechargeOrder = new RechargeOrder();
        $modelRechargeOrder->startTrans();

        try {
            $order = $modelRechargeOrder->findOrEmpty($orderId)->toArray();
            if (!$order) {
                throw new Exception('订单不存在!');
            }

            $user  = $modelUser->findOrEmpty($order['user_id'])->toArray();
            if (!$user) {
                throw new Exception('用户不存在!');
            }

            if ($order['refund_status'] != PayEnum::REFUND_NOT) {
                throw new Exception('订单已退款了!');
            }

            // 更新订单信息,标记已发起退款状态,具体退款成功看退款日志
            RechargeOrder::update([
                'id' => $order['id'],
                'refund_status' => PayEnum::REFUND_SUCCESS,
            ]);

            // 扣减用户余额和累计消费金额
            $decChatNumber    = ($user['balance']>$order['chat_balance'])     ? $order['chat_balance']   : $user['balance'];
            $decRobotNumber   = ($user['robot_num']>$order['robot_number'])   ? $order['robot_number']   : $user['robot_num'];
            $decVideoDuration = ($user['video_num']>$order['video_duration']) ? $order['video_duration'] : $user['video_num'];
            User::update([
                'balance'   => ['dec', $decChatNumber],
                'robot_num' => ['dec', $decRobotNumber]
            ], ['id'=>$user['id']]);

            // 记录账户流水-对话
            if ($decChatNumber > 0) {
                $changeType = AccountLogEnum::UM_DEC_REFUND;
                UserAccountLog::add($user['id'], $changeType, AccountLogEnum::DEC, $decChatNumber, $order['order_sn'], '充值退款退回余额');
            }

            // 记录账户流水-机器人
            if ($decRobotNumber > 0) {
                $changeType = AccountLogEnum::ROBOT_DEC_REFUND;
                UserAccountLog::add($user['id'], $changeType, AccountLogEnum::DEC, $decRobotNumber, $order['order_sn'], '充值退款减少机器人');
            }

            // 记录账户流水-数字人
            if ($decVideoDuration > 0) {
                $changeType = AccountLogEnum::VIDEO_DEC_REFUND;
                UserAccountLog::add($user['id'], $changeType, AccountLogEnum::DEC, $decVideoDuration, $order['order_sn'], '充值退款减少视频合成时长');
            }

            // 生成退款记录
            $recordSn = generate_sn(RefundRecord::class, 'sn');
            $record = RefundRecord::create([
                'sn'             => $recordSn,
                'user_id'        => $order['user_id'],
                'order_id'       => $order['id'],
                'order_sn'       => $order['order_sn'],
                'order_type'     => RefundEnum::ORDER_TYPE_RECHARGE,
                'order_amount'   => $order['order_amount'],
                'refund_amount'  => $order['order_amount'],
                'refund_type'    => RefundEnum::TYPE_ADMIN,
                'transaction_id' => $order['transaction_id'] ?? '',
                'refund_way'     => RefundEnum::getRefundWayByPayWay($order['pay_way'])
            ]);

            // 退款
            RefundLogic::refund($order, $record['id'], $order['order_amount'], $adminId);

            $modelRechargeOrder->commit();
            return true;
        } catch (Exception $e) {
            $modelRechargeOrder->rollback();
            return $e->getMessage();
        }
    }
}