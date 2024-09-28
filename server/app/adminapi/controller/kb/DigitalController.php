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
use app\adminapi\lists\kb\KbDigitalLists;
use app\adminapi\logic\kb\KbDigitalLogic;
use app\adminapi\validate\IDMustValidate;
use think\response\Json;

/**
 * 数字人管理
 */
class DigitalController extends BaseAdminController
{
    /**
     * @notes 数字人列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists(new KbDigitalLists());
    }

    /**
     * @notes 数字人详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        (new IDMustValidate())->goCheck();
        $id = intval($this->request->get('id'));

        $detail = KbDigitalLogic::detail($id);
        if (!$detail) {
            return $this->fail('数据丢失!');
        }
        return $this->data($detail);
    }

    /**
     * @notes 数字人删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        (new IDMustValidate())->post()->goCheck();
        $id = intval($this->request->post('id'));

        $result = KbDigitalLogic::del($id);
        if (!$result) {
            return $this->fail(KbDigitalLogic::getError());
        }
        return $this->success('删除成功');
    }

    /**
     * @notes 状态修改
     * @return Json
     * @author fzr
     */
    public function changeStatus(): Json
    {
        (new IDMustValidate())->post()->goCheck();
        $id = intval($this->request->post('id'));

        $result = KbDigitalLogic::changeStatus($id);
        if ($result === false) {
            return $this->fail(KbDigitalLogic::getError());
        }

        return $this->success(KbDigitalLogic::getError(), [], 1, 1);
    }
}