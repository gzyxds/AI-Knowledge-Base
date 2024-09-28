<?php
namespace app\adminapi\logic\market;
use app\common\service\ConfigService;

/**
 * 活动奖励逻辑类
 * Class ActivityRewardLogic
 * @package app\adminapi\logic\market
 */
class ActivityRewardLogic
{

    /**
     * @notes 分享奖励设置
     * @return array
     * @author cjhao
     * @date 2024/7/16 12:01
     */
    public function getShareSetting()
    {
        return [
            'is_open'           => ConfigService::get('share_award', 'is_open'),
            'one_award'         => ConfigService::get('share_award', 'one_award'),
            'day_num'           => ConfigService::get('share_award', 'day_num'),
        ];
    }


    /**
     * @notes 分享奖励设置
     * @param array $params
     * @return true
     * @author cjhao
     * @date 2024/7/16 12:02
     */
    public function setShareSetting(array $params)
    {
        ConfigService::set('share_award', 'is_open',$params['is_open']);
        ConfigService::set('share_award', 'one_award',$params['one_award']);
        ConfigService::set('share_award', 'day_num',$params['day_num']);
        return true;
    }


    /**
     * @notes 获取邀请奖励设置
     * @return array
     * @author cjhao
     * @date 2024/7/16 14:32
     */
    public function getInviteSetting(){
        return [
            'is_open'           => ConfigService::get('invite_award', 'is_open'),
            'one_award'         => ConfigService::get('invite_award', 'one_award'),
            'day_num'           => ConfigService::get('invite_award', 'day_num'),
        ];
    }

    /**
     * @notes 设置邀请奖励设置
     * @param array $params
     * @return true
     * @author cjhao
     * @date 2024/7/16 14:35
     */
    public function setInviteSetting(array $params)
    {
        ConfigService::set('invite_award', 'is_open',$params['is_open']);
        ConfigService::set('invite_award', 'one_award',$params['one_award']);
        ConfigService::set('invite_award', 'day_num',$params['day_num']);
        return true;
    }

    /**
     * @notes 获取签到设置
     * @param array $params
     * @return array
     * @author cjhao
     * @date 2024/7/16 14:42
     */
    public function getSignSetting()
    {
        return [
            'is_open' => ConfigService::get('sign_award', 'is_open'),
            'one_award' => ConfigService::get('sign_award', 'one_award')
        ];
    }

    /**
     * @notes 设置签到设置
     * @param array $params
     * @return true
     * @author cjhao
     * @date 2024/7/16 14:42
     */
    public function setSignSetting(array $params)
    {
        ConfigService::set('sign_award', 'is_open',$params['is_open']);
        ConfigService::set('sign_award', 'one_award',$params['one_award']);
        return true;
    }

    /**
     * @notes 获取作品分享配置
     * @return array[]
     * @author cjhao
     * @date 2024/7/16 14:59
     */
    public function getWorkSetting()
    {
        return [
            'draw_award'    => [
                'is_open'           => ConfigService::get('draw_award', 'is_open'),
                'one_award'         => ConfigService::get('draw_award', 'one_award'),
                'day_num'           => ConfigService::get('draw_award', 'day_num'),
//                'auto_audit'        => ConfigService::get('draw_award', 'auto_audit'),
            ],
            'music_award'       => [
                'is_open'           => ConfigService::get('music_award', 'is_open'),
                'one_award'         => ConfigService::get('music_award', 'one_award'),
                'day_num'           => ConfigService::get('music_award', 'day_num'),
//                'auto_audit'        => ConfigService::get('music_award', 'auto_audit'),
            ],
            'video_award'       => [
                'is_open'           => ConfigService::get('video_award', 'is_open'),
                'one_award'         => ConfigService::get('video_award', 'one_award'),
                'day_num'           => ConfigService::get('video_award', 'day_num'),
//                'auto_audit'        => ConfigService::get('music_award', 'auto_audit'),
            ],
        ];
    }

    /**
     * @notes 设置工作分享设置
     * @param array $params
     * @return true
     * @author cjhao
     * @date 2024/7/16 15:04
     */
    public function setWorkSetting(array $params)
    {
        ConfigService::set('draw_award', 'is_open',$params['draw_award']['is_open']);
        ConfigService::set('draw_award', 'one_award',$params['draw_award']['one_award']);
        ConfigService::set('draw_award', 'day_num',$params['draw_award']['day_num']);
//        ConfigService::set('draw_award', 'auto_audit',$params['draw_award']['auto_audit']);

        ConfigService::set('music_award', 'is_open',$params['music_award']['is_open']);
        ConfigService::set('music_award', 'one_award',$params['music_award']['one_award']);
        ConfigService::set('music_award', 'day_num',$params['music_award']['day_num']);
//        ConfigService::set('music_award', 'auto_audit',$params['music_award']['auto_audit']);

        ConfigService::set('video_award', 'is_open',$params['video_award']['is_open']);
        ConfigService::set('video_award', 'one_award',$params['video_award']['one_award']);
        ConfigService::set('video_award', 'day_num',$params['video_award']['day_num']);
//        ConfigService::set('video_award', 'auto_audit',$params['video_award']['auto_audit']);
        return true;
    }

    /**
     * @notes 获取邀请奖励设置
     * @return array
     * @author cjhao
     * @date 2024/7/16 14:32
     */
    public function getRoBotSetting(){
        return [
            'is_open'           => ConfigService::get('robot_award', 'is_open'),
            'one_award'         => ConfigService::get('robot_award', 'one_award'),
            'day_num'           => ConfigService::get('robot_award', 'day_num'),
//            'auto_audit'        => ConfigService::get('robot_award', 'auto_audit'),
        ];
    }

    /**
     * @notes 设置邀请奖励设置
     * @param array $params
     * @return true
     * @author cjhao
     * @date 2024/7/16 14:35
     */
    public function setRobotSetting(array $params)
    {
        ConfigService::set('robot_award', 'is_open',$params['is_open']);
        ConfigService::set('robot_award', 'one_award',$params['one_award']);
        ConfigService::set('robot_award', 'day_num',$params['day_num']);
//        ConfigService::set('robot_award', 'auto_audit',$params['auto_audit']);
        return true;
    }

    /**
     * @notes 获取广场设置
     * @return array[]
     * @author cjhao
     * @date 2024/7/30 13:45
     */
    public function getSquareSetting()
    {
        return [
            'draw_award'    => [
                'is_show_user'      => ConfigService::get('draw_award', 'is_show_user'),
                'auto_audit'        => ConfigService::get('draw_award', 'auto_audit'),
            ],
            'music_award'       => [
                'is_show_user'      => ConfigService::get('music_award', 'is_show_user'),
                'auto_audit'        => ConfigService::get('music_award', 'auto_audit'),
            ],
            'video_award'       => [
                'is_show_user'      => ConfigService::get('video_award', 'is_show_user'),
                'auto_audit'        => ConfigService::get('video_award', 'auto_audit'),
            ],
        ];

    }

    /**
     * @notes 设置广场设置
     * @param array $params
     * @return void
     * @author cjhao
     * @date 2024/7/30 13:50
     */
    public function setSquareSetting(array $params)
    {
        ConfigService::set('draw_award', 'is_show_user',$params['draw_award']['is_show_user']);
        ConfigService::set('draw_award', 'auto_audit',$params['draw_award']['auto_audit']);
        ConfigService::set('music_award', 'is_show_user',$params['music_award']['is_show_user']);
        ConfigService::set('music_award', 'auto_audit',$params['music_award']['auto_audit']);
        ConfigService::set('video_award', 'is_show_user',$params['video_award']['is_show_user']);
        ConfigService::set('video_award', 'auto_audit',$params['video_award']['auto_audit']);
    }

}