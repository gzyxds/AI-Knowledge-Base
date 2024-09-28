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

namespace app\adminapi\controller;

use app\adminapi\logic\WorkbenchLogic;
use think\response\Json;

/**
 * 工作台
 */
class WorkbenchController extends BaseAdminController
{
    /**
     * @notes 工作台
     * @return Json
     * @author 段誉
     * @date 2021/12/29 17:01
     */
    public function index(): Json
    {
        $result = WorkbenchLogic::index();
        return $this->data($result);
    }
}