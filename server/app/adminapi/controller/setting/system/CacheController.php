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

namespace app\adminapi\controller\setting\system;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\system\CacheLogic;
use think\response\Json;

/**
 * 系统缓存
 */
class CacheController extends BaseAdminController
{
    /**
     * @notes 清除系统缓存
     * @return Json
     * @author 段誉
     * @date 2022/4/8 16:34
     */
    public function clear(): Json
    {
         CacheLogic::clear();
         return $this->success('清除成功', [], 1, 1);
    }
}