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
use app\adminapi\lists\chat\ChatCategoryLists;
use app\adminapi\logic\chat\ChatCategoryLogic;
use app\adminapi\validate\chat\ChatCategoryValidate;
use think\response\Json;

/**
 * 示例分类控制器类
 */
class ChatCategoryController extends BaseAdminController
{
    /**
     * @notes 示例分类列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new ChatCategoryLists()));
    }

    /**
     * @notes 示例分类详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        (new ChatCategoryValidate())->get()->goCheck('id');
        $id = intval($this->request->get('id'));
        $detail = ChatCategoryLogic::detail($id);
        return $this->data($detail);
    }

    /**
     * @notes 示例分类新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new ChatCategoryValidate())->post()->goCheck('add');
        $result = ChatCategoryLogic::add($params);
        if ($result === false) {
            return $this->fail(ChatCategoryLogic::getError());
        }
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 示例分类编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new ChatCategoryValidate())->post()->goCheck();
        $result = ChatCategoryLogic::edit($params);
        if ($result === false) {
            return $this->fail(ChatCategoryLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 示例删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new ChatCategoryValidate())->post()->goCheck('id');
        $result = ChatCategoryLogic::del(intval($params['id']));
        if ($result === false) {
            return $this->fail(ChatCategoryLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 示例分类状态
     * @return Json
     * @author fzr
     */
    public function status(): Json
    {
        $params = (new ChatCategoryValidate())->post()->goCheck('id');
        $result = ChatCategoryLogic::status(intval($params['id']));
        if ($result === false) {
            return $this->fail(ChatCategoryLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }
}