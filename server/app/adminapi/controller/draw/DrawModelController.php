<?php

namespace app\adminapi\controller\draw;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\draw\DrawModelLists;
use app\adminapi\logic\draw\DrawModelLogic;
use app\adminapi\validate\draw\DrawModelValidate;
use think\response\Json;

class DrawModelController extends BaseAdminController
{
    /**
     * @notes 模型列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new DrawModelLists()));
    }

    /**
     * @notes 模型详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        (new DrawModelValidate())->get()->goCheck('id');
        $id = intval($this->request->get('id'));
        $detail = DrawModelLogic::detail($id);
        return $this->data($detail);
    }

    /**
     * @notes 模型新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new DrawModelValidate())->post()->goCheck('add');
        $result = DrawModelLogic::add($params);
        if ($result === false) {
            return $this->fail(DrawModelLogic::getError());
        }
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 模型编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new DrawModelValidate())->post()->goCheck('edit');
        $result = DrawModelLogic::edit($params);
        if ($result === false) {
            return $this->fail(DrawModelLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 示例删除
     * @return Json
     * @author fzr
     */
    public function delete(): Json
    {
        $params = (new DrawModelValidate())->post()->goCheck('id');
        $result = DrawModelLogic::delete($params['id']);
        if ($result === false) {
            return $this->fail(DrawModelLogic::getError());
        }
        return $this->success('删除成功', [$params['id']], 1, 1);
    }

    /**
     * @notes 模型状态
     * @return Json
     * @author fzr
     */
    public function status(): Json
    {
        $params = (new DrawModelValidate())->post()->goCheck('id');
        $result = DrawModelLogic::status(intval($params['id']));
        if ($result === false) {
            return $this->fail(DrawModelLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function getSdModel(): Json
    {
        $result = DrawModelLogic::getSdModel();
        return $this->success('', $result);
    }

    /**
     * @notes 获取sd微调模型
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function getSdLora(): Json
    {
        $result = DrawModelLogic::getSdLora();
        return $this->success('', $result);
    }
}