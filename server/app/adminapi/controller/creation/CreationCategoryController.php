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

namespace app\adminapi\controller\creation;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\creation\CreationCategoryLists;
use app\adminapi\logic\creation\CreationCategoryLogic;
use app\adminapi\validate\creation\CreationCategoryValidate;
use think\response\Json;

/**
 * 创作分类管理
 */
class CreationCategoryController extends BaseAdminController
{
    /**
     * @notes 创作分类列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new CreationCategoryLists()));
    }

    /**
     * @notes 创作分类详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $params = (new CreationCategoryValidate())->get()->goCheck('id');
        $result = CreationCategoryLogic::detail(intval($params['id']));
        return $this->data($result);
    }

    /**
     * @notes 创作分类新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new CreationCategoryValidate())->post()->goCheck('add');
        $result = CreationCategoryLogic::add($params);
        if ($result === false) {
            return $this->fail(CreationCategoryLogic::getError());
        }
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 创作分类编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new CreationCategoryValidate())->post()->goCheck();
        $result = CreationCategoryLogic::edit($params);
        if ($result === false) {
            return $this->fail(CreationCategoryLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 创作分类删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new CreationCategoryValidate())->post()->goCheck('id');
        $result = CreationCategoryLogic::del(intval($params['id']));
        if ($result === false) {
            return $this->fail(CreationCategoryLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 创作状态修改
     * @return Json
     * @author fzr
     */
    public function status(): Json
    {
        $params = (new CreationCategoryValidate())->post()->goCheck('id');
        $result = CreationCategoryLogic::status(intval($params['id']));
        if ($result === false) {
            return $this->fail(CreationCategoryLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }
}