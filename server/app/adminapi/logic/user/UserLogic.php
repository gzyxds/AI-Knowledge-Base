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

namespace app\adminapi\logic\user;

use app\api\service\UserTokenService;
use app\common\cache\UserTokenCache;
use app\common\enum\member\MemberPackageEnum;
use app\common\enum\PayEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\BaseLogic;
use app\common\logic\UserMemberLogic;
use app\common\model\kb\KbKnow;
use app\common\model\kb\KbKnowTeam;
use app\common\model\kb\KbRobot;
use app\common\model\member\MemberAdjustLog;
use app\common\model\member\MemberOrder;
use app\common\model\member\MemberPackage;
use app\common\model\member\UserMember;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\model\user\UserAuth;
use app\common\model\user\UserGroup;
use app\common\model\user\UserSession;
use app\common\pgsql\KbEmbedding;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\UserService;
use Exception;
use think\facade\Config;
use think\facade\Db;

/**
 * 用户逻辑层
 */
class UserLogic extends BaseLogic
{
    /**
     * @notes 用户详情
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DbException
     * @author 段誉
     * @date 2022/9/22 16:32
     */
    public static function detail(int $userId): array
    {
        $modelUser = new User();
        $user = $modelUser
            ->field([
                'id,group_ids,sn,account,nickname,avatar,real_name,channel,sex',
                'mobile,email,balance,video_num,robot_num,total_chat,is_distribution,total_space',
                'is_disable,is_blacklist,multipoint_login,login_ip,login_time,create_time,inviter_id,first_leader'
            ])
            ->append(['inviter_name','invite_num'])
            ->where(['id' => $userId])
            ->findOrEmpty();

        // 用户分组
        $user->group_name = '-';
        $user->group_ids = $user->group_ids ? explode(',', $user->group_ids) : [];
        $user->group_ids = array_map(function ($val) { return intval($val); }, $user->group_ids);
        if ($user->group_ids) {
            $groupName = (new UserGroup())->whereIn('id', $user->group_ids)->column('name')??[];
            $user->group_name = implode(',', $groupName);
        }

        // 机器人数
        $modelKbRobot = new KbRobot();
        $useRobotNum = $modelKbRobot->where(['user_id'=>$userId])->count();
        $user['total_robot_num'] = $useRobotNum + $user['robot_num'];
        $user['use_robot_num']   = $useRobotNum;
        $user['balance']   = format_amount_zero($user['balance']);

        // 来源渠道
        $user['channel'] = UserTerminalEnum::getTerminalDesc($user['channel']);
        $user->sex = $user->getData('sex');
        $user->package_name = '';
        $user->package_is_overdue = '';
        $user->package_time = '';
        $memberPackage = UserMemberLogic::getUserMember($user['id'],true);
        if($memberPackage){
            $user->package_id = $memberPackage['id'];
            $user->package_name = $memberPackage['name'];
            $user->package_time = $memberPackage['is_perpetual'] ? '永久' : date('Y-m-d H:i:s',$memberPackage['member_end_time']);
            $user->member_end_time = $memberPackage['member_end_time'];
            $user->package_is_overdue = $memberPackage['is_overdue'];
            $user->is_perpetual = $memberPackage['is_perpetual'];
        }
        //分销
        $user['is_distribution_desc'] = '未开通';
        if ($user['is_distribution']) {
            $user['is_distribution_desc'] = '已开通';
        }
        $user['leader_nickname'] = '';
        if($user['first_leader']){
            $user['leader_nickname'] = User::where(['id'=>$user['first_leader']])->value('nickname');
        }

        // 已使用空间
        $user['use_space'] = (new KbEmbedding())->where(['user_id'=>$userId, 'is_delete'=>0])->count();

        return $user->toArray();
    }

    /**
     * @notes 更新用户信息
     * @param array $params
     * @return User
     * @author 段誉
     * @date 2022/9/22 16:38
     */
    public static function edit(array $params): User
    {
        return User::update([
            'id' => $params['id'],
            $params['field'] => $params['value']
        ]);
    }

    /**
     * @notes 创建新的用户
     * @param array $params
     * @return bool
     * @author fzr
     */
    public static function createUser(array $params): bool
    {
        try {
            $userSn       = User::createUserSn();
            $passwordSalt = Config::get('project.unique_identification');
            $password     = create_password($params['password'], $passwordSalt);
            $account      = 'u' . $userSn;

            $modelUser = new User();
            if (!empty($params['mobile'])) {
                $isMobile = $modelUser->where(['mobile' => $params['mobile']])->findOrEmpty();
                if (!$isMobile->isEmpty()) {
                    throw new Exception('手机已被占用,换一个吧！');
                }
            }

            if (!empty($params['email'])) {
                $isEmail = $modelUser->where(['email' => $params['email']])->findOrEmpty();
                if (!$isEmail->isEmpty()) {
                    throw new Exception('邮箱已被占用,换一个吧！');
                }
            }

            $user = User::create([
                'sn'        => $userSn,
                'avatar'    => FileService::setFileUrl($params['avatar']),
                'real_name' => $params['real_name'] ?? '',
                'nickname'  => $params['nickname']  ?? '',
                'mobile'    => $params['mobile'] ?? '',
                'email'     => $params['email']  ??'',
                'account'   => $account,
                'password'  => $password,
                'channel'   => UserTerminalEnum::PC,
                'group_ids' => implode(',', ($params['group_ids']??[]))
            ]);

            // 用户注册记录
            UserService::registerReward($user['id']);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 加入黑名单
     * @param int $userId
     * @return bool
     * @author fzr
     */
    public static function blacklist(int $userId): bool
    {
        try {
            $modelUser = new User();
            $user = $modelUser
                ->field(['id,is_blacklist'])
                ->where(['id'=>$userId])
                ->findOrEmpty()
                ->toArray();

            if (!$user) {
                throw new Exception('用户不存在了');
            }

            User::update([
                'is_blacklist' => !$user['is_blacklist']
            ], ['id'=>$userId]);

            if ($user['is_blacklist']) {
                self::setError('移出成功');
            } else {
                $session = (new UserSession())->where(['user_id'=>$userId])->select()->toArray();

                $time = time() - 1;
                UserSession::update([
                    'expire_time' => $time,
                    'update_time' => $time
                ], ['user_id'=>$userId]);

                $userTokenCache = (new UserTokenCache());
                foreach ($session as $item) {
                    $userTokenCache->deleteUserInfo($item['token']);
                }

                self::setError('加入成功');
            }

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 重置密码
     * @param int $userId
     * @param string $password
     * @return bool
     * @autor fzr
     */
    public static function resetPwd(int $userId, string $password): bool
    {
        try {
            $modelUser = new User();
            $user = $modelUser
                ->field(['id'])
                ->where(['id'=>$userId])
                ->findOrEmpty()
                ->toArray();

            if (!$user) {
                throw new Exception('用户不存在了');
            }

            $passwordSalt = Config::get('project.unique_identification');
            $password     = create_password($password, $passwordSalt);

            User::update([
                'password'  => $password
            ], ['id'=>$userId]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 设置分组
     * @param array $ids
     * @param array $groupIds
     * @return bool
     * @autor fzr
     */
    public static function setGroup(array $ids, array $groupIds): bool
    {
        try {
            if (empty($ids)) {
                throw new Exception('请勾选需设置的用户!');
            }

            (new User())
                ->whereIn('id', $ids)
                ->update(['group_ids'=>implode(',', $groupIds)]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 调整账户
     * @param array $params
     * @param int $adminId
     * @return bool
     * @author fzr
     */
    public static function adjustAccount(array $params, int $adminId): bool
    {
        $scene  = $params['scene'];
        $action = $params['action'];
        $num    = $params['num'];
        $userId = $params['user_id'];
        $remark = $params['remark']??'';

        $modelUser = new User();
        $modelUser->startTrans();
        try {
            $user = $modelUser->find($userId);
            switch ($scene) {
                case 'balance':
                    if (AccountLogEnum::INC == $action) {
                        $user->balance += $num;
                        $user->save();
                        $changeType = AccountLogEnum::UM_INC_ADMIN;
                        UserAccountLog::add($user->id, $changeType, AccountLogEnum::INC, $num, '', $remark, [], $adminId);
                    } else {
                        if ($user->balance < $num) {
                                throw new Exception('最多只能扣减'.$user->balance.'个电力值');
                        }
                        $user->balance -= $num;
                        $user->save();
                        $changeType = AccountLogEnum::UM_DEC_ADMIN;
                        UserAccountLog::add($user->id, $changeType, AccountLogEnum::DEC, $num, '', $remark, [], $adminId);
                    }
                    break;
                case 'robot':
                    if (AccountLogEnum::INC == $action) {
                        $user->robot_num += $num;
                        $user->save();
                        $changeType = AccountLogEnum::ROBOT_INC_ADMIN;
                        UserAccountLog::add($user->id, $changeType, AccountLogEnum::INC, $num, '', $remark, [], $adminId);
                    } else {
                        if ($user->robot_num < $num) {
                            throw new Exception('最多只能扣减'.$user->robot_num.'个机器人');
                        }
                        $user->robot_num -= $num;
                        $user->save();
                        $changeType = AccountLogEnum::ROBOT_DEC_ADMIN;
                        UserAccountLog::add($user->id, $changeType, AccountLogEnum::DEC, $num, '', $remark, [], $adminId);
                    }
                    break;
                case 'video':
                    if (AccountLogEnum::INC == $action) {
                        $user->video_num += $num;
                        $user->save();
                        $changeType = AccountLogEnum::VIDEO_INC_ADMIN;
                        UserAccountLog::add($user->id, $changeType, AccountLogEnum::INC, $num, '', $remark, [], $adminId);
                    } else {
                        if ($user->video_num < $num) {
                            throw new Exception('当前用户可合成视频时长仅剩'.$user->video_num);
                        }
                        $user->video_num -= $num;
                        $user->save();
                        $changeType = AccountLogEnum::VIDEO_DEC_ADMIN;
                        UserAccountLog::add($user->id, $changeType, AccountLogEnum::DEC, $num, '', $remark, [], $adminId);
                    }
                    break;
            }

            $modelUser->commit();
            return true;
        } catch (Exception $e) {
            $modelUser->rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 调整会员到期时间
     * @param array $params
     * @return bool
     * @author ljj
     * @date 2023/4/14 4:11 下午
     */
    public static function adjustMember(array $params): bool
    {
        try {
            Db::startTrans();
            $userId = $params['id'];
            $memberEndTime =  $params['member_end_time'] ?? '';
            $memberEndTime && $memberEndTime = strtotime($memberEndTime);
            $memberPerpetual =  $params['member_perpetual'] ?? '';
            $memberPackageId =  $params['member_package_id'] ?? '';
            $now = time();

            //查看当前的会员
            $userMember = UserMemberLogic::getUserMember($userId);
            $originalUserMember = [];
            if($userMember){
                $originalUserMember = UserMember::where(['id'=>$userMember['user_member_id']])->value('package_info');
            }

            //会员清空
            if(empty($memberEndTime) && empty($memberPerpetual)){

                //如果当前有会员，清掉用户等级
                if($userMember){
                    //如果没有套餐，把这个用户的所有套餐设置为过期
                    UserMember::where(['id'=>$userMember['user_member_id']])
                        ->update(['is_perpetual'=>0,'member_end_time'=>time()]);

                    //记录管理员操作
                    MemberAdjustLog::create([
                        'user_id'   => $params['id'],
                        'operate_id'   => $params['admin_id'],
                        'package_id'   => 0,
                        'member_end_time'   => 0,
                        'is_perpetual'   => 0,
                        'package_snap'      => $originalUserMember
                    ]);
                    UserMember::where(['id'=>$userMember['user_member_id']])
                        ->update(['is_perpetual'=>0,'member_end_time'=>time()]);
                }
                //把用户已过期会员清除掉
                UserMember::where(['user_id'=>$userId,'is_perpetual'=>0,'is_clear'=>0])->where('member_end_time','<=',time())
                    ->update(['is_clear'=>1,'clear_time'=>time()]);
            }else{
                if(empty($memberPackageId)){
                    throw new Exception('请选择会员等级');
                }
                if('' === $memberPerpetual && '' === $memberEndTime){
                    throw new Exception('请选择会员时长');
                }
                if( 1 != $memberPerpetual && $memberEndTime && $now > $memberEndTime){
                    throw new Exception('会员套餐过期时间不能小于现在');
                }
                //添加会员开通记录
                $memberPackage = MemberPackage::where(['id'=>$memberPackageId])
                    ->findOrEmpty()
                    ->toArray();
                if(empty($memberPackage)){
                    throw new Exception('会员套餐错误，请重新选择');
                }
                //拼接会员价格
                $priceList = [
                    'duration_type'     => MemberPackageEnum::DURATION_PERPEUTAL,
                    'duration'          => '',
                    'sell_price'        => 0,
                    'lineation_price'   => 0,
                    'give_balance'      => 0,
                    'give_robot'        => 0,
                    'is_recommend'      => 0,
                    'status'            => 1,
                    'tags'              => '',
                    'create_time'       => $now,
                ];
                if( 0 == $memberPerpetual){
                    $difference = $memberEndTime - $now;
                    $days = intval($difference / (60 * 60 * 24));
                    // 假设每个月平均30天来计算月数
                    // $months = floor($days / 30);
                    $priceList['duration_type'] = MemberPackageEnum::DURATION_TYPE_DAY;
                    $priceList['duration'] = $days;
                }
                $memberPackage['member_end_time'] = $memberEndTime;
                $memberPackage['is_perpetual'] = $memberPerpetual;
                $memberPackage['price_list'] = $priceList;
                //已经是会员情况下
                if($userMember){
                    //不能在开通小于当前套餐的
                    $memberLists = MemberPackage::order('sort asc')
                        ->column('id');
                    foreach ($memberLists as $key => $value){
                        if($value == $userMember['id']){
                            break;
                        }
                        unset($memberLists[$key]);
                    }
                    if(!in_array($memberPackageId,$memberLists)){
                        throw new Exception('用户当前套餐高于开通套餐，无法开通');
                    }
                    //更新当前的套餐记录
                    UserMember::where(['id'=>$userMember['user_member_id']])->update([
                        'user_id' => $userId,
                        'package_name' => $memberPackage['name'] ?? '',
                        'member_end_time' => $memberEndTime,
                        'package_id' => $memberPackageId,
                        'package_price_id' => 0,
                        'is_perpetual' => $memberPerpetual,
                        'package_info' => $memberPackage,
                    ]);

                }else{

                    //如果是空会员，则创建
                    if(empty($userMember)){
                        //重新查询用户会员记录，这次包括清除掉的记录也查询出来
                        $userMemberLog = UserMember::where(['user_id'=>$userId,'package_id'=>$memberPackageId])
                            ->findOrEmpty();
                    }
                    if(!$userMemberLog->isEmpty()){
                        //更新当前的套餐记录
                        UserMember::where(['id'=>$userMemberLog['id']])->update([
                            'user_id' => $userId,
                            'package_name' => $memberPackage['name'] ?? '',
                            'member_end_time' => $memberEndTime,
                            'package_id' => $memberPackageId,
                            'package_price_id' => 0,
                            'is_clear'      => 0,
                            'clear_time'        => '',
                            'is_perpetual' => $memberPerpetual,
                            'package_info' => json_encode($memberPackage,JSON_UNESCAPED_UNICODE),
                        ]);
                    }else{
                        UserMember::create([
                            'user_id' => $userId,
                            'package_name' => $memberPackage['name'] ?? '',
                            'member_end_time' => $memberEndTime,
                            'package_id' => $memberPackageId,
                            'package_price_id' => 0,
                            'is_perpetual' => $memberPerpetual,
                            'package_info' => json_encode($memberPackage,JSON_UNESCAPED_UNICODE),
                        ]);
                    }
                }
                //记录管理员操作
                MemberAdjustLog::create([
                    'user_id'   => $params['id'],
                    'operate_id'   => $params['admin_id'],
                    'package_id'   => $memberPackageId,
                    'member_end_time'   => $memberEndTime,
                    'is_perpetual'   => $memberPerpetual,
                    'package_snap'      => $originalUserMember
                ]);
            }

            Db::commit();
            return true;

        }catch (Exception $e){
            Db::rollback();
            return $e->getMessage();
        }

    }

    /**
     * @notes 调整邀请人
     * @param array $params
     * @return bool|string
     * @author ljj
     * @date 2024/1/10 11:48 上午
     */
    public static function adjustLeader(array $params): bool|string
    {
        Db::startTrans();
        try {
            switch ($params['adjust_type']) {
                case 1://指定邀请人
                    if ($params['leader_id'] == $params['id']) {
                        throw new \Exception('不能成为自己的邀请人');
                    }

                    $leader = User::where(['id'=>$params['leader_id']])->findOrEmpty()->toArray();
                    $first_leader = $params['leader_id'];
                    $second_leader = $leader['first_leader'];
                    break;
                case 2://设置邀请人为系统
                    $first_leader = 0;
                    $second_leader = 0;
                    break;
            }
            //绑定新上级关系
            User::update(['inviter_id'=>$first_leader,'first_leader'=>$first_leader,'second_leader'=>$second_leader],['id'=>$params['id']]);

            //更新用户下级的分销关系
            User::update(['second_leader'=>$first_leader],['first_leader'=>$params['id']]);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 调整知识库空间
     * @param array $params
     * @return bool
     */
    public static function adjustSpace(array $params): bool
    {
        try {
            $ids = is_array($params['id']) ? $params['id'] : [intval($params['id'])];
            $action = intval($params['action']);
            $number = intval($params['number']) * 1024;

            $pgEmbedding = new KbEmbedding();
            if ($action == 1) {
                // 增加
                User::update([
                    'total_space' => ['inc', $number],
                    'update_time' => time()
                ], [['id', 'in', $ids]]);
            } elseif ($action == 2) {
                // 减少
                $users = (new User())->whereIn('id', $ids)->select()->toArray();
                foreach ($users as $user) {
                    $useSpace = $pgEmbedding->where(['user_id'=>$user['id'], 'is_delete'=>0])->count();
                    $surplus = $user['total_space'] - $useSpace;
                    if (count($users) == 1 and $surplus < $number) {
                        throw new Exception('扣减数值不能大于用户剩余数值: ' . $surplus);
                    }

                    if ($surplus < $number) {
                        continue;
                    }

                    User::update([
                        'total_space' => ['dec', $number],
                        'update_time' => time()
                    ], ['id'=>$user['id']]);
                }
            } else {
                // -1不限制
                User::update([
                    'total_space' => -1,
                    'update_time' => time()
                ], [['id', 'in', $ids]]);
            }
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 购买记录
     * @param $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2024/7/1 16:28
     */
    public static function buyLog($userId): array
    {
        //后台开通的
        $memberLists = MemberAdjustLog::where(['user_id'=>$userId])
            ->column('id,type,package_id,member_end_time,is_perpetual,package_snap,create_time','create_time');
        $sortLists = [];
        $packageList = MemberPackage::column('name','id');
        foreach ($memberLists as $key => $memberList){
            $sortLists[] = $key;
            //原套餐
            $packageSnap = $memberList['package_snap'];
            if($memberList['package_id']){
                if($memberList['is_perpetual']){
                    $memberLists[$key]['package_long_time'] = "永久";
                }
                if(empty($packageSnap)){
                    $memberLists[$key]['original_package_long_time'] = intval(ceil(($memberList['member_end_time'] - $key) / (60 * 60 * 24))).'天';
                }else{
                    if(0 == $memberList['is_perpetual'] && $memberList['member_end_time']){

                        $memberEndTime = $packageSnap['member_end_time'] ?? 0;
                        $memberLists[$key]['original_package_long_time'] = intval(ceil(($memberList['member_end_time'] - $memberEndTime) / (60 * 60 * 24))).'天';
                    }else{
                        $memberLists[$key]['original_package_long_time'] = '永久';
                    }
                }
            }else{
                $memberLists[$key]['package_long_time'] = "-";
                $memberLists[$key]['original_package_long_time'] = "-";
            }
//            if($memberList['id'] == 166){
//                dd($memberLists[$key]);
//            }

            if(1 == $memberList['type']){
                $memberLists[$key]['channel_text'] = '系统调整';
                $memberLists[$key]['refund_status_text'] = '系统调整';

            }else{
                $memberLists[$key]['channel_text'] = '卡密兑换';
                $memberLists[$key]['refund_status_text'] = '';

                $memberPrice = $packageSnap['price_list'] ?? [];
                if($memberPrice){
                    switch ($memberPrice['duration_type']){
                        case MemberPackageEnum::DURATION_TYPE_DAY:
                            $memberLists[$key]['original_package_long_time'] = $memberPrice['duration'].'天';
                            break;
                        case MemberPackageEnum::DURATION_TYPE_MONTH:
                            $memberLists[$key]['original_package_long_time'] = $memberPrice['duration'].'个月';
                            break;
                        case MemberPackageEnum::DURATION_PERPEUTAL:
                            $memberLists[$key]['original_package_long_time'] = '永久';
                            break;
                    }
                }

            }
            $memberLists[$key]['package_name'] = $packageList[$memberList['package_id']] ?? '无';
            $memberLists[$key]['pay_way_text'] = '';
            $memberLists[$key]['create_time'] = date('Y-m-d H:i:s',$key);
            $memberLists[$key]['pay_time_text'] = '';
            $memberLists[$key]['package_long_time'] = date('Y-m-d H:i:s',$memberList['member_end_time']);

            $memberLists[$key]['package_info'] = null;
            $memberLists[$key]['channel'] = 2;

        }
        //订单记录
        $orderLists = MemberOrder::where(['pay_status'=>PayEnum::ISPAID,'user_id'=>$userId])
            ->append(['member_package','refund_status_text','pay_way_text','pay_time_text','pay_status_text','original_package_long_time','package_long_time'])
            ->hidden(['member_package_info'])
            ->order(['id'=>'desc'])
            ->field('order_sn,refund_status,member_package_id,pay_status,pay_time,order_amount,create_time,pay_way,member_package_info,package_end_time,user_id')
            ->select()->toArray();

        $orderLists = array_column($orderLists,null,'create_time');
        $orderSortLists = [];
        foreach ($orderLists as $key => $list){
            $sortLists[] = strtotime($list['create_time']);
            $list['package_name'] = $packageList[$list['member_package_id']] ?? '';
            $list['channel'] = 1;
            $list['channel_text'] = '自行购买';
//            $list['original_package_long_time'] = '-';
            $orderSortLists[strtotime($list['create_time'])] = $list;
        }
        //按订单时间大到小排序
        arsort($sortLists);
        $lists = [];
        foreach ($sortLists as $sort){
            $lists[] = $memberLists[$sort] ?? $orderSortLists[$sort] ?? [];
        }
        return $lists;
    }

    /**
     * @notes 用户注销
     * @param int $userId
     * @return string|true
     * @author cjhao
     * @date 2024/7/2 15:35
     */
    public static function cancelled(int $userId): bool|string
    {
        $modelUser = new User();
        $modelUser->startTrans();
        try {
            if(empty($userId)){
                throw new Exception('请选择用户');
            }
            $isCancelled = ConfigService::get('user_config', 'is_cancelled', 1);
            if (!$isCancelled) {
                throw new Exception('后台已经关闭注销账号功能', 10006);
            }

            $token = UserSession::where(['user_id'=>$userId])->value('token');
            // 设置token过期
            if ($token) {
                UserTokenService::expireToken($token);
            }

            // 删除账号
            // User::update(['cancelled_remark'=> ''],['id'=>$userId]);
            User::destroy($userId);

            // 删除微信授权
            (new UserAuth())->where(['user_id'=>$userId])->delete();

            // 删除知识库团队
            $kbIds = (new KbKnow())->where('user_id', $userId)->column('id');
            if ($kbIds) {
                (new KbKnowTeam())
                    ->whereIn('kb_id', $kbIds)
                    ->update([
                        'delete_time' => time()
                    ]);
            }

            $modelUser->commit();
            return true;
        } catch (Exception $e) {
            $modelUser->rollback();
            return $e->getMessage();
        }
    }
}