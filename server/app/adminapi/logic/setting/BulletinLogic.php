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

/**
 * 公共配置逻辑类
 */
class BulletinLogic extends BaseLogic
{
    /**
     * @notes 获取公告配置
     * @return array
     * @author ljj
     * @date 2023/7/6 4:06 下午
     */
    public static function detail(): array
    {
        return [
            // 公告弹窗
            'is_bulletin'      => ConfigService::get('bulletin', 'is_bulletin', 0),
            // 公告内容
            'bulletin_content' => ConfigService::get('bulletin', 'bulletin_content', ''),
        ]??[];
    }

    /**
     * @notes 设置公告配置
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/7/6 4:10 下午
     */
    public static function save($params): bool
    {
        ConfigService::set('bulletin', 'is_bulletin', $params['is_bulletin']);
        ConfigService::set('bulletin', 'bulletin_content', $params['bulletin_content']);
        return true;
    }
}