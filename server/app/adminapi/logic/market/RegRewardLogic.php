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

namespace app\adminapi\logic\market;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;
use Exception;
use think\facade\Config;

/**
 * 注册奖励逻辑类
 */
class RegRewardLogic extends BaseLogic
{
    /**
     * @notes 注册奖励配置详情
     * @return array
     * @author fzr
     */
    public static function detail(): array
    {
        $defaultRewardChat = Config::get('project.register_rewards.reward_chat', 0);
        $defaultRewardRobot = Config::get('project.register_rewards.reward_robot', 0);

        return [
            // 功能状态
            'status'        => ConfigService::get('register_reward', 'status', 1),
            // 赠送余额
            'reward_chat'   => ConfigService::get('register_reward', 'reward_chat', $defaultRewardChat),
            // 赠送机器人
            'reward_robot'  => ConfigService::get('register_reward', 'reward_robot', $defaultRewardRobot),
            // 赠送数字人时长 (这个暂时用不上了)
            'reward_video'  => ConfigService::get('register_reward', 'reward_video', 0)
        ]??[];
    }

    /**
     * @notes 注册奖励配置保存
     * @param array $params
     * @return bool
     * @author fzr
     */
    public static function save(array $params): bool
    {
        try {
            if (isset($params['status']) && !in_array($params['status'], [0, 1])) {
                throw new Exception('开关状态异常');
            }

            if (isset($params['reward_chat']) && $params['reward_chat'] < 0) {
                throw new Exception('赠送余额数不能少于0');
            }

            if (isset($params['reward_robot']) && $params['reward_robot'] < 0) {
                throw new Exception('赠送机器人数不能少于0');
            }

            if (isset($params['reward_video']) && $params['reward_video'] < 0) {
                throw new Exception('赠送视频合成时长不能少于0');
            }

            // 功能状态
            ConfigService::set('register_reward', 'status', $params['status']??0);
            // 赠送余额
            ConfigService::set('register_reward', 'reward_chat', $params['reward_chat'] ?? 0);
            // 赠送机器人
            ConfigService::set('register_reward', 'reward_robot', $params['reward_robot'] ?? 0);
            // 赠送数字人时长
            ConfigService::set('register_reward', 'reward_video', $params['reward_video'] ?? 0);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return true;
        }
    }
}