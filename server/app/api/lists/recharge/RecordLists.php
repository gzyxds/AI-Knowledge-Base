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

namespace app\api\lists\recharge;

use app\api\lists\BaseApiDataLists;
use app\common\enum\PayEnum;
use app\common\model\recharge\RechangeCardCodeLog;
use app\common\model\recharge\RechargeOrder;

/**
 * 购买记录
 */
class RecordLists extends BaseApiDataLists
{
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
        $modelRechargeOrder = new RechargeOrder();
        $orderLists = $modelRechargeOrder
            ->field(['id,order_sn,order_amount,chat_balance,robot_number,video_duration,pay_way,pay_status,refund_status,pay_time,create_time,snapshot'])
            ->where(['pay_status'=>PayEnum::ISPAID])
            ->where(['user_id'=>$this->userId])
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select()
            ->toArray();
        $lists = [];
        $sortLists = [];
        $orderSortLists = [];
        foreach ($orderLists as &$item) {
            $snapshot = json_decode($item['snapshot'], true);
            $item['name']                = $snapshot['name']??'';
            $item['channel']             = 1;
            $item['channel_text']        = '自行购买';
            $item['sell_price']          = $snapshot['sell_price']??'0';
            $item['pay_way_text']        = PayEnum::getPayDesc($item['pay_way']);
            $item['pay_status_text']     = PayEnum::getPayStatusDesc($item['pay_status']);
            $item['pay_time']            = $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : '-';
            $item['chat_balance']        = $snapshot['chat_balance']??0;
            $item['give_chat_balance']   = $snapshot['give_chat_balance']??0;
            $item['robot_number']        = $snapshot['robot_number']??0;
            $item['video_duration']      = $snapshot['video_duration']??0;
            $item['give_robot_number']   = $snapshot['give_robot_number']??0;
            $item['give_video_duration'] = $snapshot['give_video_duration']??0;
            unset($item['snapshot']);
            $sortLists[] =  strtotime($item['create_time']);
            $orderSortLists[strtotime($item['create_time'])] = $item;


        }
        arsort($orderSortLists);
        $rechangeCardCodeLists = RechangeCardCodeLog::where(['user_id'=>$this->userId])
            ->column('*','create_time');
        foreach ($rechangeCardCodeLists as &$item) {
            $snapshot                    = $item['package_snap'];
            $item['channel']             = 2;
            $item['channel_text']        = '卡密兑换';
            $item['name']                = $snapshot['name']??'';
            $item['sell_price']          = '-';
            $item['pay_way_text']        = '-';
            $item['pay_status_text']     = '-';
            $item['pay_time']            = '-';
            $item['chat_balance']        = $snapshot['chat_balance']??0;
            $item['give_chat_balance']   = $snapshot['give_chat_balance']??0;
            $item['robot_number']        = $snapshot['robot_number']??0;
            $item['video_duration']      = $snapshot['video_duration']??0;
            $item['give_robot_number']   = $snapshot['give_robot_number']??0;
            $item['give_video_duration'] = $snapshot['give_video_duration']??0;
            $sortLists[] = $item['create_time'];
            $item['create_time']         = date('Y-m-d H:i:s',$item['create_time']);

            unset($item['package_snap']);


        }
        arsort($sortLists);
        foreach ($sortLists as $sort){
            $lists[] = $rechangeCardCodeLists[$sort] ?? $orderSortLists[$sort] ?? [];
        }
        return $lists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public function count(): int
    {
        $modelRechargeOrder = new RechargeOrder();
        return $modelRechargeOrder
            ->where(['pay_status'=>PayEnum::ISPAID])
            ->where(['user_id'=>$this->userId])
            ->count();
    }
}