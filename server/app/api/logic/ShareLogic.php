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

namespace app\api\logic;


use app\common\enum\DrawSquareEnum;
use app\common\enum\MusicSquareEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\BaseLogic;
use app\common\model\decorate\DecoratePage;
use app\common\model\draw\DrawSquare;
use app\common\model\kb\KbRobotSquare;
use app\common\model\music\MusicSquare;
use app\common\model\task\TaskInvite;
use app\common\model\task\TaskShare;
use app\common\model\task\TaskSign;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\model\video\VideoSquare;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\wechat\WeChatMnpService;
use Exception;
use think\facade\Db;

class ShareLogic extends BaseLogic
{

    /**
     * @notes 用户任务
     * @param $userId
     * @return array
     * @author cjhao
     * @date 2024/7/17 10:10
     */
    public function task($userId)
    {
        $detail = DecoratePage::findOrEmpty(10)->toArray();
        $detailData = json_decode($detail['data'],true);
        $content = $detailData[0]['content']['data'];
        $content = array_column($content,null,'type');
        $taskShare = [
            'is_open'           => ConfigService::get('share_award', 'is_open'),
            'one_award'         => ConfigService::get('share_award', 'one_award'),
            'day_num'           => ConfigService::get('share_award', 'day_num'),
            'title'             => '分享给好友',
            'num'               => 0,
        ];
        //分享好友数据
//        if($taskShare['is_open']){
            $shareNum = TaskShare::where(['user_id'=>$userId])
                ->whereDay('create_time')
                ->where('click_num','>',0)
                ->sum('click_num');
            if ($shareNum > $taskShare['day_num']) {
                $shareNum = $taskShare['day_num'];
            }
            $taskShare['num'] = $shareNum;
//        }
        //装修数据追加配置数据
        $content[3]['data'] = $taskShare;

        //邀请好友数据
        $taskInvite = [
            'is_open'           => ConfigService::get('invite_award', 'is_open'),
            'one_award'         => ConfigService::get('invite_award', 'one_award'),
            'day_num'           => ConfigService::get('invite_award', 'day_num'),
            'title'             => '邀请好友',
            'num'               => 0,
        ];
//        if($taskInvite['is_open']){
            $inviteNum = TaskShare::where(['user_id'=>$userId])
                ->whereDay('create_time')
                ->where('invite_num','>',0)
                ->sum('invite_num') ?: 0;
            if ($inviteNum > $taskInvite['day_num']) {
                $inviteNum = $taskInvite['day_num'];
            }
            $taskInvite['num'] = $inviteNum;
//        }
        $content[2]['data'] = $taskInvite;

        //每日签到数据
        $taskSign = [
            'is_open'           => ConfigService::get('sign_award', 'is_open'),
            'one_award'         => ConfigService::get('sign_award', 'one_award'),
            'day_num'           => 1,
            'title'             => '每日签到',
            'num'               => 0,
        ];
//        if($taskSign['is_open']) {
            $signNum = TaskSign::where(['user_id'=>$userId])
                ->whereDay('create_time')
                ->count();
            if ($signNum > 1) {
                $signNum = 1;
            }
            $taskSign['num'] = $signNum;
//        }
        $content[1]['data'] = $taskSign;


        //绘画分享数据
        $taskDraw = [
            'is_open'           => ConfigService::get('draw_award', 'is_open'),
            'one_award'         => ConfigService::get('draw_award', 'one_award'),
            'day_num'           => ConfigService::get('draw_award', 'day_num'),
            'title'             => '绘画分享',
            'num'               => 0,
        ];
//        if($taskDraw['is_open']) {
            $drawNum = DrawSquare::where(['operate_id'=>$userId,'source'=>DrawSquareEnum::SOURCE_USER])
                ->whereDay('create_time')
                ->group('records_id')
                ->count() ?: 0;
            if ($drawNum > $taskDraw['day_num']) {
                $drawNum = $taskDraw['day_num'];
            }
            $taskDraw['num'] = $drawNum;
//        }
        $content[4]['data'] = $taskDraw;



        //视频分享数据
        $taskVideo = [
            'is_open'           => ConfigService::get('video_award', 'is_open'),
            'one_award'         => ConfigService::get('video_award', 'one_award'),
            'day_num'           => ConfigService::get('video_award', 'day_num'),
            'title'             => '视频分享',
            'num'               => 0,
        ];
//        if($taskVideo['is_open']) {
            $videoNum = VideoSquare::where(['operate_id'=>$userId,'source'=>MusicSquareEnum::SOURCE_USER])
                ->whereDay('create_time')
                ->group('records_id')
                ->count() ?: 0;
            if ($videoNum > $taskVideo['day_num']) {
                $videoNum = $taskVideo['day_num'];
            }
            $taskVideo['num'] = $videoNum;
//        }
        $content[5]['data'] = $taskVideo;
        foreach ($content as $key => $data){
            $content[$key]['image'] = FileService::getFileUrl($data['image']);
        }


        //音乐分享数据
        $taskMusic = [
            'is_open'           => ConfigService::get('music_award', 'is_open'),
            'one_award'         => ConfigService::get('music_award', 'one_award'),
            'day_num'           => ConfigService::get('music_award', 'day_num'),
            'title'             => '音乐分享',
            'num'               => 0,
        ];
//        if($taskMusic['is_open']) {
        $musicNum = MusicSquare::where(['operate_id'=>$userId,'source'=>MusicSquareEnum::SOURCE_USER])
            ->whereDay('create_time')
            ->group('records_id')
            ->count() ?:0 ;
        if ($musicNum > $taskMusic['day_num']) {
            $musicNum = $taskMusic['day_num'];
        }
        $taskMusic['num'] = $musicNum;

//        }
        $content[6]['data'] = $taskMusic;
        //智能体
        $taskRobot = [
            'is_open'           => ConfigService::get('robot_award', 'is_open'),
            'one_award'         => ConfigService::get('robot_award', 'one_award'),
            'day_num'           => ConfigService::get('robot_award', 'day_num'),
            'title'             => '智能体分享',
            'num'               => 0,
        ];
        $robotNum = KbRobotSquare::where(['user_id'=>$userId])
            ->whereDay('create_time')
            ->count() ?: 0;
        if ($robotNum > $taskRobot['day_num']) {
            $robotNum = $taskRobot['day_num'];
        }
        $taskRobot['num'] = $robotNum;
        $content[7]['data'] = $taskRobot;
        $detail['data'] = array_values($content);
        $detail['data'] = json_encode($detail['data']);
        return $detail;
    }

    /**
     * @notes 分享接口
     * @param $userInfo
     * @return false|int[]
     * @author ljj
     * @date 2023/4/18 4:58 下午
     */
    public function share($userInfo)
    {
        try {

            $userId = $userInfo['user_id'] ?? 0;
            $taskShare = TaskShare::where(['user_id'=>$userInfo['user_id']])
                ->where('click_num','=',0)
                ->where('invite_num','=',0)
                ->whereDay('create_time')
                ->findOrEmpty();
            if($taskShare->isEmpty()){
                //获取用户客户端
                $taskShare = TaskShare::create([
                    'user_id'       => $userId,
                    'channel'       => $userInfo['terminal'] ?? 0,
                ]);
            }
            // 提交事务
            return ['share_id'=>$taskShare->id];
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * @notes 点击分享链接
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/4/18 5:13 下午
     */
    public function click($params)
    {

        try {
            $taskShare = TaskShare::where(['id'=>$params['share_id']])->findOrEmpty();
            if ($taskShare->isEmpty()) {
                return true;
            }
            $dayNum = ConfigService::get('share_award','day_num');
            $isOpen = ConfigService::get('share_award', 'is_open');
            $balance = 0;
            $num = TaskShare::where(['user_id'=>$taskShare['user_id']])
                ->where('click_num','>',0)
                ->whereDay('create_time')
                ->sum('click_num');
            if($dayNum > $num && $isOpen){
                $balance =  ConfigService::get('share_award', 'one_award');
            }
            if($balance <= 0 ){
                return true;
            }
            Db::startTrans();
            //第一次点击，增加用户余额
            User::update(['balance'=>['inc',$balance]],['id'=>$taskShare->user_id]);
            // 记录账户流水
            UserAccountLog::add($taskShare->user_id,AccountLogEnum::UM_INC_SHARE,AccountLogEnum::INC,$balance);
            $taskShare->click_num = $taskShare->click_num + 1;
            $taskShare->balance = $balance;
            $taskShare->save();

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 邀请新用户
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/4/18 5:35 下午
     */
    public function invite($params)
    {
        if ((!isset($params['share_id']) || $params['share_id'] == '') && (!isset($params['user_sn']) || $params['user_sn'] == '')) {
            return true;
        }
//        $newUser = User::where(['id'=>$params['new_user_id']])->findOrEmpty()->toArray();
//        if ((time() - strtotime($new_user['create_time'])) >= 300) {
//            return true;//当前时间与注册时间差大于5分钟，认定为非新用户
//        }
//        if ($newUser['is_new_user'] != 1) {
//            return true;//不是非新用户
//        }
        $invite = TaskInvite::where(['new_user_id'=>$params['new_user_id']])->findOrEmpty();
        if (!$invite->isEmpty()) {
            return true;//已存在邀请记录，认定为非新用户
        }
        if (isset($params['share_id'])) {
            $inviteId = TaskShare::where(['id'=>$params['share_id']])->value('user_id');
        } else {
            $inviteId = User::where(['sn'=>$params['user_sn']])->value('id');
        }
        if ($inviteId == $params['new_user_id']) {
            return true;//不能成为自己邀请人
        }
        $user = User::where(['id'=>$params['new_user_id']])->findOrEmpty();
        if($user['first_leader']){
            return true; //已经有上级
        }
        //绑定新用户上级关系
        $firstLeader = User::where(['id'=>$inviteId])->findOrEmpty()->toArray();
        User::update(['inviter_id'=>$inviteId,'first_leader'=>$inviteId,'second_leader'=>$firstLeader['first_leader']],['id'=>$params['new_user_id']]);

        Db::startTrans();
        try {
            $isOpen = ConfigService::get('invite_award', 'is_open');
            $balance = 0;
            if (1 == $isOpen) {
                $dayNum = ConfigService::get('invite_award', 'day_num');
                $num = TaskShare::where(['user_id'=>$inviteId])
                    ->where('invite_num','>',0)
                    ->whereDay('create_time')
                    ->sum('invite_num');
                if ($dayNum > $num) {
                    $balance = ConfigService::get('invite_award', 'one_award');
                }
            }
            //添加邀请记录
            TaskInvite::create([
                'user_id' => $inviteId,
                'new_user_id' => $params['new_user_id'],
                'task_share_id' => $params['share_id'] ?? 0,
                'balance' => $balance,
            ]);

            if (isset($params['share_id'])) {
                //更新分享记录
                TaskShare::update([
                    'invite_num' => ['inc',1],
                ],['id'=>$params['share_id']]);
            }



            //增加用户余额
            if ($balance > 0) {
                User::update(['balance'=>['inc',$balance]],['id'=>$inviteId]);
                // 记录账户流水
                UserAccountLog::add($inviteId,AccountLogEnum::UM_INC_INVITE,AccountLogEnum::INC,$balance);
            }

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 获取小程序码
     * @param $params
     * @return bool|string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @author ljj
     * @date 2023/5/23 11:50 上午
     */
    public function getMnpQrCode($params)
    {
        try {
            if (!isset($params['user_sn']) || $params['user_sn'] == '') {
                throw new \think\Exception('参数缺失');
            }
            if (!isset($params['page']) || $params['page'] == '') {
                throw new \think\Exception('路径缺失');
            }

            $data['page'] = $params['page'];
            $data['scene'] = 'user_sn='.$params['user_sn'];

            $result = (new WeChatMnpService())->createMpQrCode($data,'base64');
            return $result;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }



    /**
     * @notes 用户签到
     * @param $userInfo
     * @return bool
     * @author cjhao
     * @date 2024/7/18 11:05
     */
    public function sign($userInfo)
    {
        Db::startTrans();
        try {
            $isOpen           = ConfigService::get('sign_award', 'is_open');
            $oneAward         = ConfigService::get('sign_award', 'one_award');
            if(0 == $isOpen){
                throw new Exception('未开启签到功能');
            }
            $signNum = TaskSign::where(['user_id'=>$userInfo['user_id']])
                ->whereDay('create_time')
                ->count();
            if ($signNum > 0) {
                throw new Exception('今日已签到');
            }

            //添加签到记录
            TaskSign::create([
                'user_id'       => $userInfo['user_id'],
                'channel'       => $userInfo['terminal'] ?? 0,
                'balance'       => $oneAward,
            ]);
            if($oneAward > 0){
                User::update(['balance'=>['inc',$oneAward]],['id'=>$userInfo['user_id']]);
                // 记录账户流水
                UserAccountLog::add($userInfo['user_id'],AccountLogEnum::UM_INC_SIGN,AccountLogEnum::INC,$oneAward);
            }
            // 提交事务
            Db::commit();
            return true;
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }

    }

}