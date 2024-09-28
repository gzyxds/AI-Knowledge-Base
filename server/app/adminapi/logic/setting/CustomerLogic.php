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

/**
 * 客服设置逻辑
 */
class CustomerLogic extends BaseLogic
{
    /**
     * @notes 获取客服设置
     * @return array
     * @author ljj
     * @date 2022/2/15 12:05 下午
     */
    public static function getConfig(): array
    {
        $manualKf = ConfigService::get('manual_kf')??[];
        $onlineKf = ConfigService::get('online_kf')??[];
        return [
            'manual_kf' => [
                'status'       => intval($manualKf['status']??0),
                'icons'        => isset($manualKf['icons'])   ? FileService::getFileUrl($manualKf['icons'])   : '',
                'qr_code'      => isset($manualKf['qr_code']) ? FileService::getFileUrl($manualKf['qr_code']) : '',
                'title'        => $manualKf['title']??['value'=>'', 'status'=>0],
                'phone'        => $manualKf['phone']??['value'=>'', 'status'=>0],
                'service_time' => $manualKf['service_time']??['value'=>'', 'status'=>0]
            ],
            'online_kf'   => [
                'status' => intval($onlineKf['status']??0),
                'link'   => trim($onlineKf['link']??''),
                'icons'  => isset($onlineKf['icons']) ? FileService::getFileUrl($onlineKf['icons']) : ''
            ]
        ]??[];
    }

    /**
     * @notes 设置客服设置
     * @param $params
     * @author ljj
     * @date 2022/2/15 12:11 下午
     */
    public static function setConfig($params)
    {
        $manualKf = $params['manual_kf']??[];
        $onlineKf = $params['online_kf']??[];

        $allowField = ['title', 'phone', 'service_time'];
        foreach($manualKf as $key => $value) {
            if(in_array($key, $allowField)) {
                ConfigService::set('manual_kf', $key, json_encode($value, JSON_UNESCAPED_UNICODE));
            }
        }
        ConfigService::set('manual_kf', 'status', $manualKf['status']??0);
        ConfigService::set('manual_kf', 'icons', FileService::setFileUrl($manualKf['icons']??''));
        ConfigService::set('manual_kf', 'qr_code', FileService::setFileUrl($manualKf['qr_code']??''));

        ConfigService::set('online_kf', 'status', $onlineKf['status']??0);
        ConfigService::set('online_kf', 'link', $onlineKf['link']??'');
        ConfigService::set('online_kf', 'icons', FileService::setFileUrl($onlineKf['icons']??''));
    }
}