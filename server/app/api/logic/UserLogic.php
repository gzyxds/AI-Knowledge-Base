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

namespace app\api\logic;

use app\api\service\UserTokenService;

use app\common\{enum\draw\DrawEnum,
    enum\LoginEnum,
    enum\MusicEnum,
    enum\notice\NoticeEnum,
    enum\user\UserTerminalEnum,
    enum\VideoEnum,
    enum\YesNoEnum,
    logic\BaseLogic,
    logic\UserMemberLogic,
    model\draw\DrawRecords,
    model\kb\KbKnow,
    model\kb\KbKnowTeam,
    model\music\MusicRecord,
    model\notice\NoticeRecord,
    model\user\User,
    model\user\UserAuth,
    model\video\VideoRecord,
    pgsql\KbEmbedding,
    service\ConfigService,
    service\EmailService,
    service\FileService,
    service\sms\SmsDriver,
    service\wechat\WeChatMnpService};
use Exception;
use think\facade\Config;

/**
 * 会员逻辑层
 */
class UserLogic extends BaseLogic
{
    /**
     * @notes 个人中心
     * @param array $userInfo
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/16 18:04
     */
    public static function center(array $userInfo): array
    {
        $user = (new User())
            ->field([
                'id,sn,sex,account,nickname,real_name,avatar,mobile,email,is_blacklist',
                'is_new_user,create_time,password,balance,video_num,robot_num,total_chat,first_leader',
                'total_space'
            ])
            ->where(['id' => $userInfo['user_id']])
            ->findOrEmpty();

        if (in_array($userInfo['terminal'], [UserTerminalEnum::WECHAT_MMP, UserTerminalEnum::WECHAT_OA,UserTerminalEnum::PC])) {
            $auth = (new UserAuth())->where(['user_id'=>$userInfo['user_id'], 'terminal'=>$userInfo['terminal']])->find();
            $user['is_auth'] = $auth ? YesNoEnum::YES : YesNoEnum::NO;
        }

        $user['has_password'] = !empty($user['password']);
        $user->hidden(['password']);
        $user->package_name = '';
        $user->package_time = '';
        $user->package_is_overdue = '';
        $memberPackage = UserMemberLogic::getUserMember($userInfo['user_id'],true);
        if($memberPackage){
            $user->package_name = $memberPackage['name'];
//            $userMember = UserMember::where(['user_id'=>$userInfo['user_id'], 'package_id'=>$memberPackage['id']])
//                ->find();
            $user->package_time = $memberPackage['is_perpetual'] ? '永久' : date('Y-m-d H:i:s',$memberPackage['member_end_time']);
            $user->package_is_overdue = $memberPackage['is_overdue'] ;
        }
        $user->balance = format_amount_zero($user->balance);
        $user->video_num = format_amount_zero($user->video_num);
        $musicNum = MusicRecord::where(['user_id' => $userInfo['user_id'], 'status' => MusicEnum::STATUS_SUCCESS])->count();
        $videoNum = VideoRecord::where(['user_id' => $userInfo['user_id'], 'status' => VideoEnum::STATUS_SUCCESS])->count();
        $drawNum = DrawRecords::where(['user_id' => $userInfo['user_id'], 'status' => DrawEnum::STATUS_SUCCESS])->count();
        $user->my_works = $musicNum + $videoNum + $drawNum;

//        $auth = self::hasWechatAuth($userInfo['user_id']);
//        $user['is_auth'] = $auth ? YesNoEnum::YES : YesNoEnum::NO;
        $user['leader_nickname'] = '';
        if($user['first_leader']){
            $user['leader_nickname'] = User::where(['id'=>$user['first_leader']])->value('nickname');
        }

        // 已用空间数
        $pgEmbedding = new KbEmbedding();
        $useSpace = $pgEmbedding->where(['user_id'=>$user['id'], 'is_delete'=>0])->count();
        $user['use_space'] = $useSpace;

        // 未读通知数量
        $unread = (new NoticeRecord())
            ->where(['read'=>0])
            ->where(['scene_id'=>[NoticeEnum::CHAT_FEEDBACK,NoticeEnum::SQUARE_NOTICE]])
            ->where(['user_id'=>$user['id']])
            ->where(['recipient'=>1])
            ->where(['send_type'=>1])
            ->count();
        $user['unread_notice'] = $unread;
        return $user->toArray();
    }

    /**
     * @notes 个人信息
     * @param int $userId
     * @return array
     * @author 段誉
     * @date 2022/9/20 19:45
     */
    public static function info(int $userId): array
    {
        $user = (new User())
            ->field([
                'id,sn,sex,account,password,nickname,real_name,avatar,mobile',
                'is_blacklist,create_time,balance,video_num,robot_num,total_chat'
            ])
            ->where(['id' => $userId])
            ->findOrEmpty();

        $user['has_password'] = !empty($user['password']);
        $user['has_auth'] = self::hasWechatAuth($userId);
        $user['version']  = config('project.version');
        $user->hidden(['password']);

        $user->balance = format_amount_zero($user->balance);
        $user->video_num = format_amount_zero($user->video_num);
        return $user->toArray();
    }

    /**
     * @notes 设置用户信息
     * @param int $userId
     * @param array $params
     * @return User|false
     * @author 段誉
     * @date 2022/9/21 16:53
     */
    public static function setInfo(int $userId, array $params): User|bool
    {
        try {
            if ($params['field'] === 'avatar') {
                $params['value'] = FileService::setFileUrl($params['value']);
            }

            if ($params['field'] === 'nickname') {
                $params['value'] = htmlentities($params['value']);
            }

            return User::update([
                'id' => $userId,
                $params['field'] => $params['value']]
            );
        } catch (Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 是否有微信授权信息
     * @param int $userId
     * @return bool
     * @author 段誉
     * @date 2022/9/20 19:36
     */
    public static function hasWechatAuth(int $userId): bool
    {
        //是否有微信授权登录
        $terminal = [UserTerminalEnum::WECHAT_MMP, UserTerminalEnum::WECHAT_OA,UserTerminalEnum::PC];
        $auth = (new UserAuth())
            ->where(['user_id' => $userId])
            ->whereIn('terminal', $terminal)
            ->findOrEmpty();

        return !$auth->isEmpty();
    }

    /**
     * @notes 重置登录密码
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2022/9/16 18:06
     */
    public static function resetPassword(array $params): bool
    {
        try {
            // 校验验证码
            $where = [];
            //短信验证码
            if ($params['scene'] == LoginEnum::MOBILE_PASSWORD) {
                $result = (new SmsDriver())->verify($params['mobile'], $params['code'], NoticeEnum::FIND_LOGIN_PASSWORD_CAPTCHA);
                if (!$result) {
                    throw new \Exception('验证码错误');
                }
                $where = ['mobile' => $params['mobile']];
            }
            //邮箱验证码
            if ($params['scene'] == LoginEnum::EMAIL_PASSWORD) {
                $result = (new EmailService())->verify($params['email'], $params['code'], NoticeEnum::FIND_LOGIN_PASSWORD_CAPTCHA);
                if (!$result) {
                    throw new \Exception('验证码错误');
                }
                $where = ['email' => $params['email']];
            }
            // 重置密码
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);

            // 更新
            (new User())
                ->where($where)
                ->update([
                    'password' => $password
                ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 修改密码
     * @param array $params
     * @param int $userId
     * @return bool
     * @author 段誉
     * @date 2022/9/20 19:13
     */
    public static function changePassword(array $params, int $userId): bool
    {
        try {
            $user = (new User())->findOrEmpty($userId);
            if ($user->isEmpty()) {
                throw new Exception('用户不存在');
            }

            // 密码盐
            $passwordSalt = Config::get('project.unique_identification');

            if (!empty($user['password'])) {
                if (empty($params['old_password'])) {
                    throw new Exception('请填写旧密码');
                }
                $oldPassword = create_password($params['old_password'], $passwordSalt);
                if ($oldPassword != $user['password']) {
                    throw new Exception('原密码不正确');
                }
            }

            // 保存密码
            $password = create_password($params['password'], $passwordSalt);
            $user->password = $password;
            $user->save();

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 获取小程序手机号
     * @param array $params
     * @return bool
     * @throws @\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @author 段誉
     * @date 2023/2/27 11:49
     */
    public static function getMobileByMnp(array $params): bool
    {
        try {
            $response = (new WeChatMnpService())->getUserPhoneNumber($params['code']);
            $phoneNumber = $response['phone_info']['purePhoneNumber'] ?? '';
            if (empty($phoneNumber)) {
                throw new Exception('获取手机号码失败');
            }

            $user = (new User())->where([
                ['mobile', '=', $phoneNumber],
                ['id', '<>', $params['user_id']]
            ])->findOrEmpty();

            if (!$user->isEmpty()) {
                throw new Exception('手机号已被其他账号绑定');
            }

            // 绑定手机号
            User::update([
                'id' => $params['user_id'],
                'mobile' => $phoneNumber
            ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 绑定手机号
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2022/9/21 17:28
     */
    public static function bindMobile(array $params): bool
    {
        try {
            // 变更手机号场景
            $sceneId = NoticeEnum::CHANGE_MOBILE_CAPTCHA;
            $where = [
                ['id', '=', $params['user_id']],
                ['mobile', '=', $params['mobile']]
            ];

            // 绑定手机号场景
            if ($params['type'] == 'bind') {
                $sceneId = NoticeEnum::BIND_MOBILE_CAPTCHA;
                $where = [
                    ['mobile', '=', $params['mobile']]
                ];
            }

            // 校验短信
            $checkSmsCode = (new SmsDriver())->verify($params['mobile'], $params['code'], $sceneId);
            if (!$checkSmsCode) {
                throw new Exception('验证码错误');
            }

            $user = (new User())->where($where)->findOrEmpty();
            if (!$user->isEmpty()) {
                throw new Exception('该手机号已被使用');
            }

            User::update([
                'id' => $params['user_id'],
                'mobile' => $params['mobile'],
            ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 账号注销
     * @param $params
     * @param $userInfo
     * @return bool|string
     * @author fzr
     */
    public static function cancelled($params, $userInfo): bool|string
    {
        $modelUser = new User();
        $modelUser->startTrans();
        try {
            $is_cancelled = ConfigService::get('user_config', 'is_cancelled', 1);
            if (!$is_cancelled) {
                throw new Exception('后台已经关闭注销账号功能', 10006);
            }

            // 设置token过期
            if (isset($userInfo['token'])) {
                UserTokenService::expireToken($userInfo['token']);
            }

            // 删除账号
            User::update(['cancelled_remark'=>$params['cancelled_remark'] ?? ''],['id'=>$userInfo['user_id']]);
            User::destroy($userInfo['user_id']);

            // 删除微信授权
            (new UserAuth())->where(['user_id'=>$userInfo['user_id']])->delete();

            // 删除知识库团队
            $kbIds = (new KbKnow())->where('user_id', $userInfo['user_id'])->column('id');
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