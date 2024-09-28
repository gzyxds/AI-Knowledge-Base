<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\api\logic;
use app\common\enum\CardCodeEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\BaseLogic;
use app\common\model\cardcode\CardCode;
use app\common\model\cardcode\CardCodeRecord;
use app\common\model\member\MemberAdjustLog;
use app\common\model\member\MemberPackage;
use app\common\model\member\MemberPackagePrice;
use app\common\model\member\UserMember;
use app\common\model\recharge\RechangeCardCodeLog;
use app\common\model\recharge\RechargePackage;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use think\Exception;
use think\facade\Cache;
use think\facade\Db;

/**
 * 卡密兑换逻辑类
 * Class CardCodeLogic
 * @package app\api\logic
 */
class CardCodeLogic extends BaseLogic
{


    /**
     * @notes 获取卡密
     * @param string $sn
     * @param string $userId
     * @return array|string
     * @author cjhao
     * @date 2023/7/11 16:29
     */
    public function checkCard(string $sn,int $userId)
    {
        try{
            $cardCode = $this->checkSn($sn)['card_code'];
            $content = '';
            $validTime = '';
            $now = time();
            switch ($cardCode->type){
                case CardCodeEnum::TYPE_MEMBER:
                    $memberPackge = MemberPackagePrice::alias('MPP')
                        ->join('member_package MP','MPP.package_id = MP.id')
                        ->where(['MPP.id'=>$cardCode->relation_id])
                        ->field('name,duration,package_id,duration_type')
                        ->findOrEmpty();
                    if($memberPackge->isEmpty()){
                        throw new Exception('卡密套餐已失效');
                    }
                    $duration = $memberPackge['duration'] ?? '';
                    $durationType = $memberPackge['duration_type'] ?? 0;
                    $content = $memberPackge['name'] ?? '';
                    $cardCode->package_id = $memberPackge['package_id'];
                    $addTime = '';
                    $now = time();
                    switch ($durationType){
                        case MemberPackageEnum::DURATION_TYPE_DAY:
                            $content.= '('.$duration.'天)';
                            $addTime = strtotime("+{$duration} day",$now);
                            break;
                        case MemberPackageEnum::DURATION_TYPE_MONTH:
                            $content.= '('.$duration.'个月)';
                            $addTime = strtotime("+{$duration} month",$now);
                            break;
                        case MemberPackageEnum::DURATION_PERPEUTAL:
                            $content.= '(永久)';
                            break;
                    }
                    $userMember = UserMember::where(['user_id'=>$userId,'package_id'=>$cardCode->package_id])->findOrEmpty();
                    if(0 == $userMember['is_perpetual'] && $userMember['member_end_time'] < $now){
                        $validTime = date('Y-m-d H:i:s',$addTime);
                    }else{

                        if(1 == $userMember['is_perpetual']){
                            $validTime = '永久';
                        }else{
                            $memberEndTime = $userMember['member_end_time'] - $now + $addTime;
                            $validTime = date('Y-m-d H:i:s',$memberEndTime);
                        }

                    }

                    break;
                case CardCodeEnum::TYPE_RECHARGE:
                    $content = RechargePackage::where(['id'=>$cardCode->relation_id])->value('name ');
                    if(empty($content)){
                        throw new Exception('卡密套餐已失效');
                    }
                    break;
                case CardCodeEnum::TYPE_BALANCE:
                    $content = $cardCode->balance.'条';
                    break;
            }
            return [
                'id'            => $cardCode->id,
                'sn'            => $cardCode->sn,
                'type'          => $cardCode->type,
                'type_desc'     => CardCodeEnum::getTypeDesc($cardCode->type),
                'content'       => $content,
                'valid_time'    => $validTime,
                'failure_time'  => date('Y-m-d H:i:s',$cardCode->valid_end_time).' 前可使用'
            ];
        }catch (Exception $e){
            return $e->getMessage();
        }

        
    }

    /**
     * @notes 卡密兑换
     * @param $sn
     * @author cjhao
     * @date 2023/7/11 17:11
     */
    public function useCard($sn,$userId)
    {
        try{

            $cache = Cache::get('card_code_'.$sn);
            Cache::set('card_code_'.$sn,$sn,2);
            if($cache){
                throw new Exception('请勿频繁操作');
            }

            Db::startTrans();
            $cardData = $this->checkSn($sn);
            $cardCode = $cardData['card_code'];
            $cardCodeRecord = $cardData['card_code_record'];
            $user = User::findOrEmpty($userId);
            //会员套餐
            if(CardCodeEnum::TYPE_MEMBER == $cardCode->type){
                $memberPrice = MemberPackagePrice::where(['id'=>$cardCode->relation_id])->findOrEmpty();
                $memberPackage = MemberPackage::where(['id'=>$memberPrice['package_id']])->findOrEmpty();
                if($memberPrice->isEmpty() || $memberPackage->isEmpty()){
                    throw new Exception('卡密兑换失败');
                }
                if(0 == $memberPrice->status || 0 == $memberPrice->status){
                    throw new Exception('卡密套餐已下架，无法兑换');
                }
                $giveBalance = 0;
                $giveRobot = 0;
                if($memberPrice['is_give']){
                    //赠送电力值
                    $giveBalance = $memberPrice['give_balance'];
                    //赠送机器人
                    $giveRobot =  $memberPrice['give_robot'];
                }
                $user->robot_num += $giveRobot;
                $user->balance += $giveBalance;
                $now = time();
                $user->save();
                //记录账户流水
                if ($giveBalance > 0) {
                    UserAccountLog::add(
                        $userId,
                        AccountLogEnum::UM_INC_MEMBER,
                        AccountLogEnum::INC,
                        $giveBalance,
                        $sn
                    );
                }
                //记录账户流水
                if ($giveRobot > 0) {
                    UserAccountLog::add(
                        $userId,
                        AccountLogEnum::ROBOT_INC_MEMBER,
                        AccountLogEnum::INC,
                        $giveRobot,
                        $sn
                    );
                }
                $duration = $memberPrice['duration'];
                $durationType = $memberPrice['duration_type'];

                $userMember = UserMember::where(['user_id'=>$userId,'package_id'=>$memberPrice['package_id']])
                    ->findOrEmpty();

                $isPerpetual = 0;
                $addTime = 0;
                //增加用户会员到期时间
                switch ($durationType) {
                    case MemberPackageEnum::DURATION_TYPE_DAY:
                        $addTime = strtotime("+{$duration} day",$now);
                        break;
                    case MemberPackageEnum::DURATION_TYPE_MONTH:
                        $addTime = strtotime("+{$duration} month",$now);
                        break;
                    case MemberPackageEnum::DURATION_PERPEUTAL:
                        $isPerpetual = 1;
                        break;
                }
                //如果用户已经是永久套餐
                if($userMember && $userMember['is_perpetual']){
                    $isPerpetual = 1;
                }
                $memberPackage['price_list'] = $memberPrice;
                //要记录到期时间，方便做记录算天数

//                $originalMemberPackage = $userMember['package_info'] ?? [];
                if($userMember->isEmpty()){
                    $memberEndTime = $addTime;
                    $memberPackage['member_end_time'] = $memberEndTime;
                    $memberPackage['is_perpetual'] = $isPerpetual;
                    //添加会员开通记录
                    UserMember::create([
                        'user_id' => $userId,
                        'package_id'=> $memberPrice['package_id'],
                        'package_price_id'=> $memberPrice['id'],
                        'package_name' => $memberPackage['name'],
                        'member_end_time' => $addTime,
                        'is_perpetual' => MemberPackageEnum::DURATION_PERPEUTAL == $durationType ? 1 : 0,
                        'package_info' => $memberPackage,
                    ]);
                }else{

                    $memberEndTime = $userMember['member_end_time'];
                    $memberPackage['member_end_time'] = $memberEndTime;
                    $memberPackage['is_perpetual'] = $isPerpetual;
                    //继上时间
                    if($addTime){
                        if ($memberEndTime > $now) {
                            $memberEndTime = $memberEndTime - $now + $addTime;
                        } else {
                            $memberEndTime = $addTime;
                        }
                    }
                    UserMember::where(['id'=>$userMember->id])->update([
                        'user_id' => $userId,
                        'package_id'=> $memberPackage['id'],
                        'package_price_id'=> $memberPrice['id'],
                        'package_name' => $memberPackage['name'],
                        'member_end_time' => 1 == $isPerpetual ? 0 : $memberEndTime,
                        'is_perpetual' => $isPerpetual,
                        'continue_time'=> $now,
                        'package_info' => $memberPackage,
                    ]);
                }

                MemberAdjustLog::create([
                    'user_id'           => $userId,
                    'operate_id'        => '',
                    'package_id'        => $memberPackage['id'],
                    'member_end_time'   => $memberEndTime,
                    'is_perpetual'      => $isPerpetual,
                    'type'              => 2,
                    'package_snap'      => $memberPackage,
                ]);
            }
            //兑换充值
            if(CardCodeEnum::TYPE_RECHARGE == $cardCode->type){

                $package = RechargePackage::where(['id'=>$cardCode->relation_id])->findOrEmpty();
                if($package->isEmpty() || 0 == $package->status){
                    throw new Exception('卡密套餐已下架，无法兑换');
                }
                // 充值数据合计
                $balance       = $package['chat_balance']   + $package['give_chat_balance'];
                $robotNum      = $package['robot_number']   + $package['give_robot_number'];
                $videoDuration = $package['video_duration'] + $package['give_video_duration'];

                // 更新账户数量
                $user->balance    += $balance;
                $user->robot_num  += $robotNum;
                $user->video_num  += $videoDuration;
                $user->save();

                // 对话数的增加
                if ($balance) {
                    $changeType   = AccountLogEnum::UM_INC_RECHARGE;
                    $changeRemark = AccountLogEnum::getChangeTypeDesc($changeType);
                    UserAccountLog::add($userId, $changeType, AccountLogEnum::INC, $balance, $sn, $changeRemark);
                }

                // 机器人数增加
                if ($robotNum) {
                    $changeType = AccountLogEnum::ROBOT_INC_RECHARGE;
                    $changeRemark = AccountLogEnum::getChangeTypeDesc($changeType);
                    UserAccountLog::add($userId, $changeType, AccountLogEnum::INC, $robotNum, $sn, $changeRemark);
                }

                // 视频合成时长
                if($videoDuration){
                    $changeType = AccountLogEnum::VIDEO_INC_RECHARGE;
                    $changeRemark = AccountLogEnum::getChangeTypeDesc($changeType);
                    UserAccountLog::add($userId, $changeType, AccountLogEnum::INC, $videoDuration, $sn, $changeRemark);
                }

                $cardCodeRecord->pachage_snapshot = $package;

                RechangeCardCodeLog::create([
                    'user_id'   => $userId,
                    'package_id'   => $package['id'],
                    'package_snap'      => json_encode($package,JSON_UNESCAPED_UNICODE),
                ]);
            }
            //兑换电力值
            if(CardCodeEnum::TYPE_BALANCE == $cardCode->type){
                $balance = $cardCode['balance'] ?? 0;
                if($balance > 0){
                    //用户添加次数
                    $user->balance += $balance;
                    $user->save();
                    //记录流水
                    UserAccountLog::add(
                        $userId,
                        AccountLogEnum::UM_INC_CARDCODE_GIVE,
                        AccountLogEnum::INC,
                        $balance,
                        $sn
                    );
                }
            }
            // 更新卡密兑换记录
            $cardCodeRecord->user_id = $userId;
            $cardCodeRecord->status = 1;
            $cardCodeRecord->use_time = time();
            $cardCodeRecord->save();

            Db::commit();
            return true;
        }catch (Exception $e){
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }

    }

    /**
     * @notes 验证卡密
     * @param $sn
     * @return array
     * @author cjhao
     * @date 2023/7/11 17:03
     */
    public function checkSn($sn)
    {

        if(empty($sn)){
            throw new Exception('查询失败，请输入卡密');
        }

        $cardCodeRecord = CardCodeRecord::where(['sn'=>$sn])->findOrEmpty();
        if($cardCodeRecord->isEmpty()) {
            throw new Exception('查询失败，卡密编号不存在');
        }
        if($cardCodeRecord->status){
            throw new Exception('查询失败，卡密已被使用');
        }
        $cardCode = CardCode::where(['id' => $cardCodeRecord->card_id])->findOrEmpty();
        $now = time();
        if($now < $cardCode->valid_start_time) {
            throw new Exception('该卡密未到生效时间');
        }
        if($cardCode->valid_end_time < $now) {
            throw new Exception('卡密已过期');
        }
        return [
            'card_code'         => $cardCode,
            'card_code_record'  => $cardCodeRecord
        ];
    }

}