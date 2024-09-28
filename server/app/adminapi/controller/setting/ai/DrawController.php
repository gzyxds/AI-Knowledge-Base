<?php

namespace app\adminapi\controller\setting\ai;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\ai\AiDrawLogic;
use app\common\enum\draw\DrawEnum;
use think\response\Json;

class DrawController extends BaseAdminController
{
    /**
     * AI模型配置详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $model = $this->request->get('model', DrawEnum::API_SD);
        $results = AiDrawLogic::detail($model);
        return $this->data($results);
    }

    /**
     * @notes AI模型配置保存
     * @return Json
     * @author fzr
     */
    public function save(): Json
    {
        $results = AiDrawLogic::save($this->request->post());
        if ($results === false) {
            return $this->fail(AiDrawLogic::getError());
        }
        return $this->success('保存成功', [], 1, 1);
    }

}