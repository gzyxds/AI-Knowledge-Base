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

namespace app\adminapi\controller\chat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\chat\ChatSampleLists;
use app\adminapi\logic\chat\ChatSampleLogic;
use app\adminapi\validate\chat\ChatSampleValidate;
use think\response\Json;

/**
 * 对话示例
 */
class ChatSampleController extends BaseAdminController
{
    /**
     * @ntoes 对话示例列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new ChatSampleLists()));
    }

    /**
     * @notes 对话示例详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $id = intval($this->request->get('id'));
        $result = ChatSampleLogic::detail($id);
        return $this->data($result);
    }

    /**
     * @notes 对话示例新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new ChatSampleValidate())->post()->goCheck('add');
        $result = ChatSampleLogic::add($params);
        if ($result === false) {
            return $this->fail(ChatSampleLogic::getError());
        }
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 对话示例编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new ChatSampleValidate())->post()->goCheck();
        $result = ChatSampleLogic::edit($params);
        if ($result === false) {
            return $this->fail(ChatSampleLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 对话示例删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new ChatSampleValidate())->post()->goCheck('id');
        $result = ChatSampleLogic::del(intval($params['id']));
        if ($result === false) {
            return $this->fail(ChatSampleLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 修改状态
     * @return Json
     * @author fzr
     */
    public function status(): Json
    {
        $params = (new ChatSampleValidate())->post()->goCheck('id');
        $result = ChatSampleLogic::status(intval($params['id']));
        if ($result === false) {
            return $this->fail(ChatSampleLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }
}