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

namespace app\adminapi\lists\order;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\RefundEnum;
use app\common\lists\ListsExtendInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\Order;
use app\common\service\FileService;
use think\db\exception\DbException;

class OrderLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExtendInterface
{
    /**
     * @notes 查询条件
     * @return array
     */
    public function queryWhere(): array
    {
        $where = [];

        // 用户编号
        if (!empty($this->params['user'])) {
            $where[] = ['U.sn|U.nickname|U.mobile', 'like', '%' . $this->params['user'] . '%'];
        }

        // 支付时间
        if (!empty($this->params['start_time']) && !empty($this->params['end_time'])) {
            $time = [strtotime($this->params['start_time']), strtotime($this->params['end_time'])];
            $where[] = ['O.pay_time', 'between', $time];
        }

        return $where;
    }

    /**
     * @notes 搜索条件
     * @return string[][]
     * @author fzr
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['O.subject', 'O.category'],
            '='      => ['O.order_terminal', 'O.order_status', 'O.refund_status']
        ] ?? [];
    }

    /**
     * @notes 列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public function lists(): array
    {
        $modelOrder = new Order();
        $lists = $modelOrder
            ->alias('O')
            ->field([
                'O.id,O.order_sn,O.order_terminal,O.order_status,O.pay_status,O.refund_status',
                'O.pay_way,O.order_amount,O.subject,O.category,O.label,O.pay_time,O.create_time',
                'O.user_id,U.sn,U.avatar,U.nickname,U.account,U.mobile'
            ])
            ->leftJoin('user U', 'U.id = O.user_id')
            ->where($this->searchWhere)
            ->where($this->queryWhere())
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['user'] = [
                'id'       => $item['user_id'],
                'sn'       => $item['sn'],
                'avatar'   => FileService::getFileUrl($item['avatar']),
                'nickname' => $item['nickname'],
                'account'  => $item['account'],
                'mobile'   => $item['mobile'],
            ];

            $item['desc'] = [
                'order_terminal' => PayEnum::getPaySceneDesc($item['order_terminal']),
                'refund_status'  => OrderEnum::getRefundStatusDesc($item['refund_status']),
                'order_status'   => OrderEnum::getOrderStatusDesc($item['order_status']),
                'pay_status'     => PayEnum::getPayStatusDesc($item['pay_status']),
                'pay_way'        => PayEnum::getPayDesc($item['pay_way'])
            ];

            $item['pay_time'] = $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : '-';

            unset($item['user_id']);
            unset($item['sn']);
            unset($item['avatar']);
            unset($item['nickname']);
            unset($item['account']);
            unset($item['mobile']);
            unset($item['pay_way']);
            unset($item['order_terminal']);
        }

        return $lists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     */
    public function count(): int
    {
        $modelOrder = new Order();
        return $modelOrder
            ->alias('O')
            ->field([
                'O.id,O.order_sn,O.order_terminal,O.order_status,O.pay_status,O.refund_status',
                'O.pay_way,O.order_amount,O.subject,O.category,O.label,O.pay_time,O.create_time',
                'O.user_id,U.sn,U.avatar,U.nickname,U.account,U.mobile'
            ])
            ->where($this->searchWhere)
            ->where($this->queryWhere())
            ->leftJoin('user U', 'U.id = O.user_id')
            ->count();
    }

    /**
     * @return array
     * @throws DbException
     */
    public function extend(): array
    {
        $field = [
            'O.id,O.order_sn,O.order_terminal,O.order_status,O.pay_status,O.refund_status',
            'O.pay_way,O.order_amount,O.subject,O.category,O.label,O.pay_time,O.create_time',
            'O.user_id,U.sn,U.avatar,U.nickname,U.account,U.mobile'
        ];

        $where = array_merge($this->searchWhere, $this->queryWhere());
        $where[] = ['pay_status', '=', PayEnum::ISPAID];

        $modelOrder = new Order();
        $totalOrderNum          = $modelOrder->alias('O')->field($field)->where($where)->leftJoin('user U', 'U.id = O.user_id')->count();
        $totalOrderAmount       = $modelOrder->alias('O')->field($field)->where($where)->leftJoin('user U', 'U.id = O.user_id')->sum('order_amount');
        $totalRefundOrderNum    = $modelOrder->alias('O')->field($field)->where($where)->where(['refund_status'=>PayEnum::REFUND_SUCCESS])->leftJoin('user U', 'U.id = O.user_id')->count();
        $totalRefundOrderAmount = $modelOrder->alias('O')->field($field)->where($where)->where(['refund_status'=>PayEnum::REFUND_SUCCESS])->leftJoin('user U', 'U.id = O.user_id')->sum('order_amount');
        $totalOrderNetIncome    = $totalOrderAmount - $totalRefundOrderAmount;

        return [
            // 订单数
            'total_order_num'     => $totalOrderNum,
            // 累计订单金额
            'total_order_amount'  => $totalOrderAmount,
            // 退款订单
            'total_refund_num'    => $totalRefundOrderNum,
            // 累计退款金额
            'total_refund_amount' => $totalRefundOrderAmount,
            // 净收入
            'total_net_income'    => $totalOrderNetIncome
        ]??[];
    }
}