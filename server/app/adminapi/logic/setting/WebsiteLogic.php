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

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;
use app\common\service\FileService;
use Exception;

/**
 * 网站设置
 */
class WebsiteLogic extends BaseLogic
{
    /**
     * @notes 获取网站信息
     * @return array
     * @author 段誉
     * @date 2021/12/28 15:43
     */
    public static function getWebsiteInfo(): array
    {
        return [
            'name'        => ConfigService::get('website', 'name'),
            'web_favicon' => FileService::getFileUrl(ConfigService::get('website', 'web_favicon')),
            'web_logo'    => FileService::getFileUrl(ConfigService::get('website', 'web_logo')),
            'login_image' => FileService::getFileUrl(ConfigService::get('website', 'login_image')),

            'shop_name' => ConfigService::get('website', 'shop_name'),                              //前台名称
            'shop_logo' => FileService::getFileUrl(ConfigService::get('website', 'shop_logo')),     //前台logo

            'pc_logo'     => FileService::getFileUrl(ConfigService::get('website', 'pc_logo')),
            'pc_name'     => ConfigService::get('website', 'pc_name'),
            'pc_title'    => ConfigService::get('website', 'pc_title'),
            'pc_ico'      => FileService::getFileUrl(ConfigService::get('website', 'pc_ico')),
            'pc_login_image' => FileService::getFileUrl(ConfigService::get('website', 'pc_login_image')),
            'pc_desc'        => ConfigService::get('website', 'pc_desc', ''),
            'pc_key'         => ConfigService::get('website', 'pc_key', ''),

            'contacts'    => ConfigService::get('website', 'contacts', ''), // 联系人
            'mobile'      =>  ConfigService::get('website', 'mobile', ''),  // 手机号码
        ]??[];
    }

    /**
     * @notes 设置网站信息
     * @param array $params
     * @author 段誉
     * @date 2021/12/28 15:43
     */
    public static function setWebsiteInfo(array $params)
    {
        $favicon  = FileService::setFileUrl($params['web_favicon']);
        $logo     = FileService::setFileUrl($params['web_logo']);
        $login    = FileService::setFileUrl($params['login_image']);
        $pcLogo   = FileService::setFileUrl($params['pc_logo']);
        $pcIco    = FileService::setFileUrl($params['pc_ico'] ?? '');
        $pcCover  = FileService::setFileUrl($params['pc_login_image'] ?? '');
        $shopLogo = FileService::setFileUrl($params['shop_logo']);


        ConfigService::set('website', 'name', $params['name']);
        ConfigService::set('website', 'web_favicon', $favicon);
        ConfigService::set('website', 'web_logo', $logo);
        ConfigService::set('website', 'login_image', $login);

        ConfigService::set('website', 'shop_name', $params['shop_name']);
        ConfigService::set('website', 'shop_logo', $shopLogo);

        ConfigService::set('website', 'pc_logo', $pcLogo);
        ConfigService::set('website', 'pc_name', $params['pc_name']);
        ConfigService::set('website', 'pc_title', $params['pc_title']);
        ConfigService::set('website', 'pc_ico', $pcIco);
        ConfigService::set('website', 'pc_login_image', $pcCover);
        ConfigService::set('website', 'pc_desc', $params['pc_desc'] ?? '');
        ConfigService::set('website', 'pc_key', $params['pc_key'] ?? '');

        ConfigService::set('website', 'contacts', $params['contacts'] ?? '');
        ConfigService::set('website', 'mobile', $params['mobile'] ?? '');
    }

    /**
     * @notes 获取版权备案
     * @return array
     * @author 段誉
     * @date 2021/12/28 16:09
     */
    public static function getCopyright() : array
    {
        return ConfigService::get('copyright', 'config', []);
    }

    /**
     * @notes 设置版权备案
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2022/8/8 16:33
     */
    public static function setCopyright(array $params): bool
    {
        try {
            if (!is_array($params['config'])) {
                throw new Exception('参数异常');
            }
            ConfigService::set('copyright', 'config', $params['config']);
            return true;
        } catch (Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 设置政策协议
     * @param array $params
     * @author ljj
     * @date 2022/2/15 10:59 上午
     */
    public static function setAgreement(array $params): void
    {
        $serviceContent = clear_file_domain($params['service_content'] ?? '');
        $privacyContent = clear_file_domain($params['privacy_content'] ?? '');
        $paymentContent = clear_file_domain($params['payment_content'] ?? '');
        $distributionContent = clear_file_domain($params['distribution_content'] ?? '');
        ConfigService::set('agreement', 'service_title', $params['service_title'] ?? '');
        ConfigService::set('agreement', 'service_content', $serviceContent);
        ConfigService::set('agreement', 'privacy_title', $params['privacy_title'] ?? '');
        ConfigService::set('agreement', 'privacy_content', $privacyContent);
        ConfigService::set('agreement', 'payment_title', $params['payment_title'] ?? '');
        ConfigService::set('agreement', 'payment_content', $paymentContent);
        ConfigService::set('agreement', 'distribution_title', $params['distribution_title'] ?? '');
        ConfigService::set('agreement', 'distribution_content', $distributionContent);
    }

    /**
     * @notes 获取政策协议
     * @return array
     * @author ljj
     * @date 2022/2/15 11:15 上午
     */
    public static function getAgreement() : array
    {
        $config = [
            'service_title'         => ConfigService::get('agreement', 'service_title'),
            'service_content'       => ConfigService::get('agreement', 'service_content'),
            'privacy_title'         => ConfigService::get('agreement', 'privacy_title'),
            'privacy_content'       => ConfigService::get('agreement', 'privacy_content'),
            'payment_title'         => ConfigService::get('agreement', 'payment_title'),
            'payment_content'       => ConfigService::get('agreement', 'payment_content'),
            'distribution_title'    => ConfigService::get('agreement', 'distribution_title'),
            'distribution_content'  => ConfigService::get('agreement', 'distribution_content'),
        ];

        $config['service_content'] = get_file_domain($config['service_content']);
        $config['privacy_content'] = get_file_domain($config['privacy_content']);
        $config['payment_content'] = get_file_domain($config['payment_content']);
        $config['distribution_content'] = get_file_domain($config['distribution_content']);
        return $config??[];
    }
}