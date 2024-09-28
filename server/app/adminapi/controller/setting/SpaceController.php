<?php

namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\SpaceLogic;
use app\adminapi\validate\setting\SpaceValidate;
use think\response\Json;

/**
 * 空间设置
 */
class SpaceController extends BaseAdminController
{
    /**
     * @notes 空间设置详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $config = SpaceLogic::detail();
        return $this->data($config);
    }

    /**
     * @notes 空间设置保存
     * @return Json
     * @author fzr
     */
    public function save(): Json
    {
        (new SpaceValidate())->post()->goCheck();
        $result = SpaceLogic::save($this->request->post());
        if ($result === false) {
            return $this->fail(SpaceLogic::getError());
        }
        return $this->success('设置成功', [], 1, 1);
    }
}