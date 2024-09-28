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

namespace app\adminapi\logic\setting;

use app\common\service\{ConfigService, FileService};

/**
 * 设置-用户设置逻辑层
 */
class UserLogic
{
    /**
     * @notes 获取用户设置
     * @return array
     * @author 段誉
     * @date 2022/3/29 10:09
     */
    public static function getConfig(): array
    {
        $defaultAvatar = FileService::getFileUrl(config('project.default_image.user_avatar'));
        return [
            // 默认头像
            'default_avatar' => FileService::getFileUrl(ConfigService::get('user', 'default_avatar', $defaultAvatar)),
            // 账号注销
            'is_cancelled' => ConfigService::get('user', 'is_cancelled', 1),
        ]??[];
    }

    /**
     * @notes 设置用户设置
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2022/3/29 10:09
     */
    public function setConfig(array $params): bool
    {
        $avatar = FileService::setFileUrl($params['default_avatar']);
        ConfigService::set('user', 'default_avatar', $avatar);
        ConfigService::set('user', 'is_cancelled', $params['is_cancelled']??1);
        return true;
    }

    /**
     * @notes 获取注册登录配置
     * @return array
     * @author 段誉
     * @date 2022/3/29 10:10
     */
    public function getRegisterConfig(): array
    {
        $config = ConfigService::get('login')??[];
        return [
            // 注册方式
            'register_way'        => $config['register_way'] ??  config('project.login.register_way'),
            // 登录方式
            'login_way'           => ConfigService::get('login', 'login_way', config('project.login.login_way')),
            // 默认登录方式: 1-手机号登录; 2-邮箱登录, 3=微信
            'default_login_way'   => ConfigService::get('login', 'default_login_way', config('project.login.default_login_way')),
            // 短信验证码
            'register_sms_verify' => ConfigService::get('login', 'register_sms_verify', config('project.login.register_sms_verify')),
            // 政策协议
            'is_agreement'        => ConfigService::get('login', 'is_agreement', config('project.login.is_agreement')),
            // 注册强制绑定手机
            'coerce_mobile' => ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile')),
        ]??[];
    }

    /**
     * @notes 设置登录注册配置
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2022/3/29 10:10
     */
    public static function setRegisterConfig(array $params): bool
    {
        // 注册方式: [1=手机号注册, 2-邮箱注册]
        ConfigService::set('login', 'register_way', $params['register_way']);
        // 登录方式: [1=手机号登录, 2=邮箱登录]
        ConfigService::set('login', 'login_way', $params['login_way']);
        // 默认登录方式:  1-手机号登录; 2-邮箱登录, 3-微信登录/公众号授权登录;
        ConfigService::set('login', 'default_login_way', $params['default_login_way']);
        // 短信验证码
        ConfigService::set('login', 'register_sms_verify', $params['register_sms_verify']);
        // 政策协议
        ConfigService::set('login', 'is_agreement', $params['is_agreement']);
        // 注册强制绑定手机
        ConfigService::set('login', 'coerce_mobile', $params['coerce_mobile']??0);
        return true;
    }
}