<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\adminapi\logic\member;


use app\common\enum\member\MemberPackageEnum;
use app\common\enum\PayEnum;
use app\common\enum\RefundEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\BaseLogic;
use app\common\logic\RefundLogic;
use app\common\model\member\MemberOrder;
use app\common\model\member\UserMember;
use app\common\model\refund\RefundRecord;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use think\facade\Db;

class MemberOrderLogic extends BaseLogic
{
    /**
     * @notes 会员订单详情
     * @param $params
     * @return array
     * @author ljj
     * @date 2023/4/21 10:11 上午
     */
    public function detail($params)
    {
        $result = MemberOrder::where(['id'=>$params['id']])
            ->with(['user'])
            ->append(['terminal_text','member_package_name','order_type_text','member_package','pay_status_text','pay_way_text','pay_time_text','refund_status_text'])
            ->findOrEmpty()
            ->toArray();

        return $result;
    }

    /**
     * @notes 退款
     * @param $params
     * @param $adminId
     * @return bool|string
     * @author ljj
     * @date 2023/4/21 10:33 上午
     */
    public static function refund($params, $adminId)
    {
        Db::startTrans();
        try {
            $order = MemberOrder::findOrEmpty($params['id'])->toArray();
            $memberPackage = json_decode($order['member_package_info'],true);
            $user = User::findOrEmpty($order['user_id'])->toArray();
            $memberPrice = $memberPackage['price_list'];
            // 更新订单信息, 标记已发起退款状态,具体退款成功看退款日志
            MemberOrder::update([
                'id' => $order['id'],
                'refund_status' => PayEnum::REFUND_SUCCESS,
            ]);

            //更新会员开通记录
            $userMember = UserMember::where(['user_id'=>$order['user_id'],'package_id'=>$order['member_package_id']])
                ->findOrEmpty()->toArray();
            //如果用户会员等级变化了，不做如何操作
            if($userMember){
                //判断会员套餐是否为永久套餐，回退套餐
                //扣减用户会员时间和累计消费金额
//                $days = date("t",$order['pay_time']);//下单当月天数
//                var_dump($memberPrice['duration']);
//                $deductTime = $memberPrice['duration'] * $days * 24 * 60 * 60;//扣减的时间
//                $memberEndTime = $userMember['member_end_time'] - $deductTime;
                $durationType = $memberPrice['duration_type'] ?? 1;
                $isPerpetual = $userMember['is_perpetual'];
                switch ($durationType) {
                    case MemberPackageEnum::DURATION_TYPE_DAY:
                        $memberPackageTime = strtotime("+{$memberPrice['duration']} day",$order['pay_time']);
                        break;
                    case MemberPackageEnum::DURATION_TYPE_MONTH:
                        $memberPackageTime = strtotime("+{$memberPrice['duration']} month",$order['pay_time']);
                        break;
                    case MemberPackageEnum::DURATION_PERPEUTAL:
                        $isPerpetual = 1;
                        break;
                }
                $addTime = $memberPackageTime - $order['pay_time'];
                $time = time();
                $memberEndTime = $userMember['member_end_time'];
                if ($userMember['member_end_time'] > $time) {
                    $memberEndTime = $userMember['member_end_time'] - $addTime;
                    if ($memberEndTime < $time) {
                        $memberEndTime = $time;
                    }
                }
                UserMember::where(['user_id'=>$order['user_id'],'package_id'=>$order['member_package_id']])
                    ->update(['member_end_time'=>$memberEndTime,'is_perpetual'=>$isPerpetual]);
            }




            //扣减赠送绘画次数
            $giveBalance = $memberPrice['give_balance'];
            if ($user['balance'] < $giveBalance) {
                $giveBalance = $user['balance'];
            }
            if ($giveBalance > 0) {
                User::update(['balance'=>['dec',$giveBalance]],['id'=>$user['id']]);
                UserAccountLog::add(
                    $user['id'],
                    AccountLogEnum::UM_DEC_MEMBER_REFUND,
                    AccountLogEnum::DEC,
                    $giveBalance,
                    $order['order_sn']
                );
            }
            //扣减赠送对话次数
            $giveRobotNum = $memberPrice['give_robot'];
            if ($user['robot_num'] < $giveRobotNum) {
                $giveRobotNum = $user['robot_num'];
            }
            if ($giveRobotNum > 0) {
                User::update(['robot_num'=>['dec',$giveRobotNum]],['id'=>$user['id']]);
                UserAccountLog::add(
                    $user['id'],
                    AccountLogEnum::ROBOT_DEC_MEMBER_REFUND,
                    AccountLogEnum::DEC,
                    $giveRobotNum,
                    $order['order_sn']
                );
            }
            // 生成退款记录
            $recordSn = generate_sn(RefundRecord::class, 'sn');
            $record = RefundRecord::create([
                'sn' => $recordSn,
                'user_id' => $order['user_id'],
                'order_id' => $order['id'],
                'order_sn' => $order['order_sn'],
                'order_type' => RefundEnum::ORDER_TYPE_MEMBER,
                'order_amount' => $order['order_amount'],
                'refund_amount' => $order['order_amount'],
                'refund_type' => RefundEnum::TYPE_ADMIN,
                'transaction_id' => $order['transaction_id'] ?? '',
                'refund_way' => RefundEnum::getRefundWayByPayWay($order['pay_way']),
            ]);

            // 退款
            RefundLogic::refund($order, $record['id'], $order['order_amount'], $adminId);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }
}