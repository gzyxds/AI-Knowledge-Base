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

namespace app\adminapi\lists\recharge;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\PayEnum;
use app\common\lists\ListsExtendInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\recharge\RechargeOrder;
use app\common\service\FileService;
use think\db\exception\DbException;

/**
 * 充值订单列表
 */
class RechargeOrderLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExtendInterface
{
    /**
     * @notes 搜索条件
     * @author fzr
     */
    public function queryWhere(): array
    {
        $where = [];

        // 订单来源
        if (!empty($this->params['terminal']) && $this->params['terminal']) {
            $where[] = ['ro.order_terminal', '=', intval($this->params['terminal'])];
        }

        // 下单时间
        if (!empty($this->params['start_time']) && !empty($this->params['end_time'])) {
            $where[] = ['ro.create_time', 'between', [strtotime($this->params['start_time']), strtotime($this->params['end_time'])]];
        }

        // 用户编号
        if (!empty($this->params['user_info']) && $this->params['user_info']) {
            $where[] = ['u.sn|u.nickname|u.mobile', 'like', '%' . $this->params['user_info'] . '%'];
        }

        return $where;
    }

    /**
     * @notes 搜索条件
     * @return array
     * @author fzr
     */
    public function setSearch(): array
    {
        return [
            '=' => ['ro.order_sn', 'ro.pay_way', 'ro.pay_status', 'ro.refund_status', 'ro.order_terminal']
        ]??[];
    }

    /**
     * @notes 充值套餐列表
     * @return array
     * @throws DbException
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public function lists(): array
    {
        $modelRechargeOrder = new RechargeOrder();
        $lists = $modelRechargeOrder
            ->alias('ro')
            ->field([
                'ro.id,ro.order_sn,ro.pay_way,ro.pay_status,ro.refund_status',
                'ro.order_amount,ro.order_terminal,ro.pay_time,ro.snapshot,ro.video_duration',
                'u.sn,u.avatar,u.nickname'
            ])
            ->where($this->queryWhere())
            ->where($this->searchWhere)
            ->where(['pay_status'=>PayEnum::ISPAID])
            ->order('ro.id', 'desc')
            ->join('user u', 'u.id = ro.user_id')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $snapshot = json_decode($item['snapshot'], true);
            $item['avatar']              = FileService::getFileUrl($item['avatar']);
            $item['pay_way_text']        = PayEnum::getPayDesc($item['pay_way']);
            $item['pay_status_text']     = PayEnum::getPayStatusDesc($item['pay_status']);
            $item['pay_time']            = empty($item['pay_time']) ? '-' : date('Y-m-d H:i:s', $item['pay_time']);
            $item['name']                = $snapshot['name']??'';
            $item['chat_balance']        = $snapshot['chat_balance']??'0';
            $item['give_chat_balance']   = $snapshot['give_chat_balance']??'0';
            $item['robot_number']        = $snapshot['robot_number']        ?? 0;
            $item['give_robot_number']   = $snapshot['give_robot_number']   ?? 0;
            $item['give_video_duration'] = $snapshot['give_video_duration'] ?? 0;
            unset($item['snapshot']);
        }

        return $lists;
    }

    /**
     * 充值订单统计
     * @return int
     * @throws DbException
     * @author fzr
     */
    public function count(): int
    {
        $modelRechargeOrder = new RechargeOrder();
        return $modelRechargeOrder
            ->alias('ro')
            ->field([
                'ro.id,ro.sn,ro.pay_way,ro.pay_status,ro.refund_status',
                'ro.order_amount,ro.order_terminal,ro.pay_time,ro.snapshot',
                'u.avatar,u.nickname'
            ])
            ->where($this->queryWhere())
            ->where($this->searchWhere)
            ->where(['pay_status'=>PayEnum::ISPAID])
            ->join('user u', 'u.id = ro.user_id')
            ->count();
    }

    /**
     * 充值订单扩展
     * @return array
     * @throws DbException
     * @author fzr
     */
    public function extend(): array
    {
        $modelRechargeOrder = new RechargeOrder();
        $rechargeOrderNumber = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->count();
        $totalRechargeAmount = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->sum('order_amount');
        $refundOrderNumber   = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->where(['refund_status'=>1])->count();
        $totalRefundAmount   = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->where(['refund_status'=>1])->sum('order_amount');
        $netIncomeAmount     = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->where(['refund_status'=>0])->sum('order_amount');

        return [
            'rechargeOrderNumber' => $rechargeOrderNumber, // 充值订单数
            'totalRechargeAmount' => $totalRechargeAmount, // 累计充值金额
            'refundOrderNumber'   => $refundOrderNumber,   // 退款订单
            'totalRefundAmount'   => $totalRefundAmount,   // 累计退款金额
            'netIncomeAmount'     => $netIncomeAmount      // 净收入
        ]??[];
    }
}