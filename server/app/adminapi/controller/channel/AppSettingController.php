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

namespace app\adminapi\controller\channel;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\channel\AppSettingLogic;
use think\response\Json;

/**
 * APP设置控制器
 */
class AppSettingController extends BaseAdminController
{
    /**
     * @notes 获取App设置
     * @return Json
     * @author 段誉
     * @date 2022/3/29 10:24
     */
    public function getConfig(): Json
    {
        $result = AppSettingLogic::getConfig();
        return $this->data($result);
    }

    /**
     * @notes App设置
     * @return Json
     * @author 段誉
     * @date 2022/3/29 10:25
     */
    public function setConfig(): Json
    {
        $params = $this->request->post();
        AppSettingLogic::setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}