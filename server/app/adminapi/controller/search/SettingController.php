<?php

namespace app\adminapi\controller\search;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\search\SettingLogic;
use think\response\Json;

/**
 * Ai搜索配置
 */
class SettingController extends BaseAdminController
{
    /**
     * @notes 基础配置详情
     * @return Json
     * @author fzr
     */
    public function basisConfig(): Json
    {
        $result = SettingLogic::basisConfig();
        return $this->data($result);
    }

    /**
     * @notes 基础配置保存
     * @return Json
     * @author fzr
     */
    public function basisSave(): Json
    {
        $result = SettingLogic::basisSave($this->request->post());
        if ($result === false) {
            return $this->fail(SettingLogic::getError());
        }
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * @notes 示例配置详情
     * @return Json
     * @author fzr
     */
    public function exampleConfig(): Json
    {
        $result = SettingLogic::exampleConfig();
        return $this->data($result);
    }

    /**
     * @notes 示例配置保存
     * @return Json
     * @author fzr
     */
    public function exampleSave(): Json
    {
        SettingLogic::exampleSave($this->request->post());
        return $this->success('设置成功', [], 1, 1);
    }
}