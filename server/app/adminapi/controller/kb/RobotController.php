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
use app\adminapi\lists\kb\KbRobotLists;
use app\adminapi\lists\kb\KbRobotShareLogLists;
use app\adminapi\logic\kb\KbRobotLogic;
use app\adminapi\validate\IDMustValidate;
use think\response\Json;

/**
 * 机器人管理
 */
class RobotController extends BaseAdminController
{
    /**
     * @notes 机器人列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new KbRobotLists()));
    }

    /**
     * @notes 机器人详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        (new IDMustValidate())->goCheck();
        $id = intval($this->request->get('id'));

        $result = KbRobotLogic::detail($id);
        return $this->data($result);
    }

    /**
     * @notes 机器人删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        (new IDMustValidate())->post()->goCheck();
        $id = intval($this->request->post('id'));

        $result = KbRobotLogic::del($id);
        if ($result === false) {
            return $this->fail(KbRobotLogic::getError());
        }

        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 修改机器人状态
     * @return Json
     * @author fzr
     */
    public function changeStatus(): Json
    {
        (new IDMustValidate())->post()->goCheck();
        $id = intval($this->request->post('id'));

        $result = KbRobotLogic::changeStatus($id);
        if ($result === false) {
            return $this->fail(KbRobotLogic::getError());
        }

        return $this->success(KbRobotLogic::getError(), [], 1, 1);
    }

    /**
     * @notes 修改广场的状态
     * @return Json
     * @author fzr
     */
    public function changePublic(): Json
    {
        (new IDMustValidate())->post()->goCheck();
        $id = intval($this->request->post('id'));

        $result = KbRobotLogic::changePublic($id);
        if ($result === false) {
            return $this->fail(KbRobotLogic::getError());
        }

        return $this->success(KbRobotLogic::getError(), [], 1, 1);
    }

    /**
     * @notes 机器人问答记录
     * @return Json
     * @author fzr
     */
    public function chatRecord(): Json
    {
        $get = $this->request->get();
        $result = KbRobotLogic::chatRecord($get);
        return $this->data($result);
    }

    /**
     * @notes 机器人问答删除
     * @return Json
     * @author fzr
     */
    public function chatClean(): Json
    {
        $ids = $this->request->post('ids', []);

        $result = KbRobotLogic::chatClean($ids);
        if ($result === false) {
            return $this->fail(KbRobotLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }



}