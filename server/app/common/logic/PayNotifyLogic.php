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

namespace app\common\logic;

use app\common\enum\member\MemberPackageEnum;
use app\common\enum\PayEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\model\member\MemberOrder;
use app\common\model\member\UserMember;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use Exception;
use think\facade\Log;

/**
 * 支付成功后处理订单状态
 */
class PayNotifyLogic extends BaseLogic
{
    public static function handle($action, $orderSn, $extra = []): bool|string
    {
        try {
            self::$action($orderSn, $extra);
            return true;
        } catch (Exception $e) {
            Log::write(implode('-', [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ]));
            self::setError($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * @notes 订单回调处理
     * @param $orderSn
     * @param array $extra
     * @throws Exception
     * @author fzr
     */
    public static function recharge($orderSn, array $extra = [])
    {
        $order = (new RechargeOrder())->where('order_sn', $orderSn)->findOrEmpty();
        $userId      = $order->user_id;
        $orderSn     = $order->order_sn;
        $snapshot    = json_decode($order->snapshot, true);

        // 充值数据合计
        $balance       = $snapshot['chat_balance']   + $snapshot['give_chat_balance'];
        $robotNum      = $snapshot['robot_number']   + $snapshot['give_robot_number'];
        $videoDuration = $snapshot['video_duration'] + $snapshot['give_video_duration'];

        // 更新账户数量
        $user = (new User())->findOrEmpty($order->user_id);
        $user->total_amount += $order->order_amount;
        $user->balance    += $balance;
        $user->robot_num  += $robotNum;
        $user->video_num  += $videoDuration;
        $user->save();

        // 对话数的增加
        if ($balance) {
            $changeType   = AccountLogEnum::UM_INC_RECHARGE;
            $changeRemark = AccountLogEnum::getChangeTypeDesc($changeType);
            UserAccountLog::add($userId, $changeType, AccountLogEnum::INC, $balance, $orderSn, $changeRemark);
        }

        // 机器人数增加
        if ($robotNum) {
            $changeType = AccountLogEnum::ROBOT_INC_RECHARGE;
            $changeRemark = AccountLogEnum::getChangeTypeDesc($changeType);
            UserAccountLog::add($userId, $changeType, AccountLogEnum::INC, $robotNum, $orderSn, $changeRemark);
        }

        // 视频合成时长
        if($videoDuration){
            $changeType = AccountLogEnum::VIDEO_INC_RECHARGE;
            $changeRemark = AccountLogEnum::getChangeTypeDesc($changeType);
            UserAccountLog::add($userId, $changeType, AccountLogEnum::INC, $videoDuration, $orderSn, $changeRemark);
        }

        // 更新充值订单状态
        $order->transaction_id = $extra['transaction_id'];
        $order->pay_status     = PayEnum::ISPAID;
        $order->pay_time       = time();
        $order->save();

        //分销
        (new DistributionLogic())->add($user,$order,1);
    }

    /**
     * @notes 会员订单支付回调
     * @param $orderSn
     * @param array $extra
     * @author ljj
     * @date 2023/4/20 6:27 下午
     */
    public static function member($orderSn, $extra = [])
    {
        $time = time();
        $order = MemberOrder::where('order_sn', $orderSn)->findOrEmpty();
        $memberPackage = $order->member_package_info;
        $memberPrice = $memberPackage['price_list'];
        //赠送电力值
        $giveBalance = $memberPrice['give_balance'];
        //赠送机器人
        $giveRobot =  $memberPrice['give_robot'];

        //是否永久套餐
        $user = User::findOrEmpty($order->user_id);
        $user->total_amount += $order->order_amount;
        $user->robot_num += $giveRobot;
        $user->balance += $giveBalance;
        $user->save();
        //记录账户流水
        if ($giveBalance > 0) {
            UserAccountLog::add(
                $user->id,
                AccountLogEnum::UM_INC_MEMBER,
                AccountLogEnum::INC,
                $giveBalance,
                $order->order_sn
            );
        }
        //记录账户流水
        if ($giveRobot > 0) {
            UserAccountLog::add(
                $order->user_id,
                AccountLogEnum::ROBOT_INC_MEMBER,
                AccountLogEnum::INC,
                $giveRobot,
                $order->order_sn
            );
        }
        $packageInfo = $order['member_package_info'];
        $duration = $packageInfo['price_list']['duration'];
        $durationType = $packageInfo['price_list']['duration_type'];
        // 更新订单支付状态
        $order->transaction_id = $extra['transaction_id'] ?? '';
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();

//        $order->save();
        $userMember = UserMember::where(['user_id'=>$order->user_id,'package_id'=>$memberPackage['id']])
            ->findOrEmpty();

        $isPerpetual = 0;
        $addTime = 0;
        $memberEndTime = '';
        //增加用户会员到期时间
        switch ($durationType) {
            case MemberPackageEnum::DURATION_TYPE_DAY:
                $addTime = strtotime("+{$duration} day",$time);
                break;
            case MemberPackageEnum::DURATION_TYPE_MONTH:
                $addTime = strtotime("+{$duration} month",$time);
                break;
            case MemberPackageEnum::DURATION_PERPEUTAL:
                $isPerpetual = 1;
                break;
        }


        if($userMember->isEmpty()){
            $memberEndTime = $addTime;
            $memberPackage['member_end_time'] = $isPerpetual ?  0 : $memberEndTime;
            $memberPackage['is_perpetual'] = $isPerpetual;
            //添加会员开通记录
            UserMember::create([
                'user_id' => $order->user_id,
                'package_id'=> $memberPackage['id'],
                'package_price_id'=> $memberPrice['id'],
                'package_name' => $memberPackage['name'],
                'member_end_time' => $addTime,
                'is_perpetual' => MemberPackageEnum::DURATION_PERPEUTAL == $durationType ? 1 : 0,
                'package_info' => $memberPackage,
            ]);
        }else{

            //如果用户已经是永久套餐
            if($userMember['is_perpetual']){
                $isPerpetual = 1;
            }else{
                $memberEndTime = $userMember['member_end_time'];
                //继上时间
                if ($memberEndTime > $time) {
                    $memberEndTime = $memberEndTime - $time + $addTime;
                } else {
                    $memberEndTime = $addTime;
                }
            }
            $memberPackage['member_end_time'] = $isPerpetual ?  0 : $memberEndTime;
            $memberPackage['is_perpetual'] = $isPerpetual;
            UserMember::where(['id'=>$userMember->id])->update([
                'user_id' => $order->user_id,
                'package_id'=> $memberPackage['id'],
                'package_price_id'=> $memberPrice['id'],
                'package_name' => $memberPackage['name'],
                'is_clear'      => 0,
                'member_end_time' => 1 == $isPerpetual ?  0 : $memberEndTime,
                'is_perpetual' => $isPerpetual,
                'continue_time'=> $time,
                'package_info' => $memberPackage,
            ]);


        }
        $order->package_end_time = $isPerpetual ?:$memberEndTime;

        $order->save();

        //分销
        (new DistributionLogic())->add($user,$order,2);



    }
}
