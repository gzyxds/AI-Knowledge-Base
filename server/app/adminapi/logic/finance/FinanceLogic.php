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

use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\Order;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\User;

/**
 * 财务管理逻辑类
 */
class FinanceLogic extends BaseLogic
{
    /**
     * @notes 数据统计
     * @return array
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public static function summary(): array
    {
        $modelUser = new User();
        $modelRechargeOrder = new RechargeOrder();

        $totalOrderNum          = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->count();
        $totalOrderAmount       = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->sum('order_amount');
        $totalRefundOrderNum    = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID, 'refund_status'=>PayEnum::REFUND_SUCCESS])->count();
        $totalRefundOrderAmount = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID, 'refund_status'=>PayEnum::REFUND_SUCCESS])->sum('order_amount');
        $totalOrderNetIncome    = $totalOrderAmount - $totalRefundOrderAmount;


        $userNum = $modelUser->count();
        $userConsumePeople = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->group('user_id')->count();

        return [
            // 经营概况
            'business_overview' => [
                // 累计收入金额
                'total_order_amount'  => round($totalOrderAmount,2),
                // 累计订单笔数
                'total_order_num'     => $totalOrderNum,
                // 累计退款金额
                'total_refund_amount' => round($totalRefundOrderAmount,2),
                // 累计退款订单数
                'total_refund_num'    => $totalRefundOrderNum,
                // 累计净收入金额
                'net_income'          => round($totalOrderNetIncome,2),
            ],
            // 用户概况
            'user_overview' => [
                // 用户总人数
                'user_num'            => $userNum,
                // 累计充值人数
                'total_recharge_num'  => $userConsumePeople,
                // 用户累计消费金额
                'user_total_amount'   => $totalOrderAmount,
                // 用户累计问答次数
                'user_total_quiz'     => $modelUser->sum('total_chat'),
                // 用户剩余对话余额
                'user_balance_chat'   => $modelUser->sum('balance'),
                // 用户剩余机器人数
                'user_robot_num'      => $modelUser->sum('robot_num')
            ]
        ]??[];
    }
}