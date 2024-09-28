<?php

namespace app\adminapi\controller\draw;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\draw\DrawModelCategoryLogic;
use app\adminapi\validate\draw\DrawModelCategoryValidate;
use think\response\Json;

class DrawModelCategoryController extends BaseAdminController
{
    /**
     * @notes 列表
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author JXDN
     * @date 2024/06/05 17:58
     */
    public function lists(): Json
    {
        $params = $this->request->get();
        $result = DrawModelCategoryLogic::lists($params);
        return $this->success('', $result);
    }

    /**
     * @notes 添加
     * @return Json
     * @author JXDN
     * @date 2024/5/15 11:18
     */
    public function add(): Json
    {
        $params = (new DrawModelCategoryValidate())->post()->goCheck('add');
        DrawModelCategoryLogic::add($params);
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 编辑
     * @return Json
     * @author JXDN
     * @date 2024/5/15 11:18
     */
    public function edit(): Json
    {
        $params = (new DrawModelCategoryValidate())->post()->goCheck('edit');
        DrawModelCategoryLogic::edit($params);
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * @notes 删除
     * @return Json
     * @throws \Exception
     * @author JXDN
     * @date 2024/06/05 17:59
     */
    public function delete(): Json
    {
        $params = (new DrawModelCategoryValidate())->post()->goCheck('delete');
        DrawModelCategoryLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 详情
     * @return Json
     * @author JXDN
     * @date 2024/06/05 17:59
     */
    public function detail(): Json
    {
        $params = (new DrawModelCategoryValidate())->goCheck('id');
        $result = DrawModelCategoryLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 状态切换
     * @return Json
     * @author JXDN
     * @date 2024/06/05 17:59
     */
    public function status(): Json
    {
        $post = (new DrawModelCategoryValidate())->post()->goCheck('id');
        DrawModelCategoryLogic::status($post['id']);
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 全部
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 10:15
     */
    public function all(): Json
    {
        $result = DrawModelCategoryLogic::all();
        return $this->success('', $result);
    }
}