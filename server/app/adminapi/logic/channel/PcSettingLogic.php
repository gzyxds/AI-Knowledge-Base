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

namespace app\adminapi\logic\channel;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

/**
 * pc端设置逻辑类
 */
class PcSettingLogic extends BaseLogic
{
    /**
     * @notes 获取pc端设置
     * @author cjhao
     * @return array
     * @date 2023/4/19 15:26
     */
    public function getConfig(): array
    {
        return [
            // 渠道状态: [0=关闭, 1=开启]
            'status'      => ConfigService::get('pc_page', 'status', 1),
            // 关闭后渠道后访问页面 0-空页面 1-自定义链接
            'page_status' => ConfigService::get('pc_page', 'page_status', 0),
            // 自定义链接
            'page_url'    => ConfigService::get('pc_page', 'page_url', ''),
            // 前端页面
            'url' => request()->domain() . '/pc'
        ]??[];
    }

    /**
     * @notes 设置pc端设置
     * @param array $params
     * @author cjhao
     * @date 2023/4/19 15:27
     */
    public function setConfig(array $params)
    {
        ConfigService::set('pc_page', 'status', $params['status']);
        ConfigService::set('pc_page', 'page_status', $params['page_status']);
        ConfigService::set('pc_page', 'page_url', $params['page_url']);
    }
}