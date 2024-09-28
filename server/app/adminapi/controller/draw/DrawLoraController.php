<?php

namespace app\adminapi\controller\draw;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\draw\DrawLoraLists;
use app\adminapi\logic\draw\DrawLoraLogic;
use app\adminapi\validate\draw\DrawLoraValidate;
use think\response\Json;

class DrawLoraController extends BaseAdminController
{
    /**
     * @notes 微调模型列表
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function lists(): Json
    {
        return $this->dataLists((new DrawLoraLists()));
    }

    /**
     * @notes 全部微调模型
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function all(): Json
    {
        $result = DrawLoraLogic::all();
        return $this->data($result);
    }

    /**
     * @notes 微调模型详情
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function detail(): Json
    {
        (new DrawLoraValidate())->get()->goCheck('id');
        $id = intval($this->request->get('id'));
        $detail = DrawLoraLogic::detail($id);
        return $this->data($detail);
    }

    /**
     * @notes 微调模型新增
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function add(): Json
    {
        $params = (new DrawLoraValidate())->post()->goCheck('add');
        $result = DrawLoraLogic::add($params);
        if ($result === false) {
            return $this->fail(DrawLoraLogic::getError());
        }
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 微调模型编辑
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function edit(): Json
    {
        $params = (new DrawLoraValidate())->post()->goCheck();
        $result = DrawLoraLogic::edit($params);
        if ($result === false) {
            return $this->fail(DrawLoraLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 微调模型删除
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function delete(): Json
    {
        $params = (new DrawLoraValidate())->post()->goCheck('id');
        $result = DrawLoraLogic::del(intval($params['id']));
        if ($result === false) {
            return $this->fail(DrawLoraLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 微调模型状态
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function status(): Json
    {
        $params = (new DrawLoraValidate())->post()->goCheck('id');
        $result = DrawLoraLogic::status(intval($params['id']));
        if ($result === false) {
            return $this->fail(DrawLoraLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 获取sd微调微调模型
     * @return Json
     * @author JXDN
     * @date 2024/5/14 17:59
     */
    public function getSdLora()
    {
        $result = DrawLoraLogic::getSdLora();
        return $this->success('', $result);
    }
}