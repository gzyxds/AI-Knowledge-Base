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

namespace app\adminapi\controller\kb;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\kb\KbTeachLists;
use app\adminapi\logic\kb\KbTeachLogic;
use think\response\Json;

/**
 * 训练数据管理
 */
class TeachController extends BaseAdminController
{
    /**
     * @notes 训练数据列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists(new KbTeachLists());
    }

    /**
     * @notes 训练数据删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $uuid = $this->request->post('uuid');
        $result = KbTeachLogic::del($uuid);
        if ($result === false) {
            return $this->fail(KbTeachLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }
}