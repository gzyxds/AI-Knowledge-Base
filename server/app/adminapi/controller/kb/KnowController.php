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
use app\adminapi\lists\kb\KbKnowLists;
use app\adminapi\logic\kb\KbKnowLogic;
use app\adminapi\validate\IDMustValidate;
use think\response\Json;

/**
 * 知识库管理
 */
class KnowController extends BaseAdminController
{
    /**
     * @notes 知识库列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new KbKnowLists()));
    }

    /**
     * @notes 知识库详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        (new IDMustValidate())->goCheck();
        $id = intval($this->request->get('id'));

        $result = KbKnowLogic::detail($id);
        return $this->data($result);
    }

    /**
     * @notes 知识库删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        (new IDMustValidate())->post()->goCheck();
        $id = intval($this->request->post('id'));

        $result = KbKnowLogic::del($id);
        if ($result === false) {
            return $this->fail(KbKnowLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 知识库转移
     * @return Json
     * @author fzr
     */
    public function transfer(): Json
    {
        (new IDMustValidate())->post()->goCheck();
        $id = intval($this->request->post('id'));
        $type = trim($this->request->post('type', ''));
        $toUserId = intval($this->request->post('user_id', 0));

        $result = KbKnowLogic::transfer($type, $id, $toUserId);
        if ($result === false) {
            return $this->fail(KbKnowLogic::getError());
        }
        return $this->success('转移成功', [], 1, 1);
    }

    /**
     * @notes 修改知识库状态
     * @return Json
     * @author fzr
     */
    public function changeStatus(): Json
    {
        (new IDMustValidate())->post()->goCheck();
        $id = intval($this->request->post('id'));

        $result = KbKnowLogic::changeStatus($id);
        if ($result === false) {
            return $this->fail(KbKnowLogic::getError());
        }

        return $this->success(KbKnowLogic::getError(), [], 1, 1);
    }

    /**
     * @notes 文件列表
     * @return Json
     * @author fzr
     */
    public function files(): Json
    {
        $get = $this->request->get();
        $result = KbKnowLogic::files($get);
        return $this->data($result);
    }

    /**
     * @notes 文件删除
     * @return Json
     * @author fzr
     */
    public function fileRemove(): Json
    {
        $kid  = intval($this->request->post('kb_id', 0));
        $fids = $this->request->post('fids', []);

        $result = KbKnowLogic::fileRemove($kid, $fids);
        if ($result === false) {
            return $this->fail(KbKnowLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 文件数据
     * @return Json
     * @author fzr
     */
    public function fileDatas(): Json
    {
        $get = $this->request->get();
        $result = KbKnowLogic::fileDatas($get);
        return $this->data($result);
    }
}