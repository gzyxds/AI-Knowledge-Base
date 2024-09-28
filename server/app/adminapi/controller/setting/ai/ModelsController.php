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

namespace app\adminapi\controller\setting\ai;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\ai\AiModelsLogic;
use app\adminapi\validate\setting\ModelsValidate;
use think\response\Json;

/**
 * AI模型配置管理
 */
class ModelsController extends BaseAdminController
{
    /**
     * @notes 模型通道
     * @return Json
     * @author fzr
     */
    public function channels(): Json
    {
        $detail = AiModelsLogic::channel();
        return $this->data($detail);
    }

    /**
     * @notes 模型列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        $lists = AiModelsLogic::lists();
        return $this->data($lists);
    }

    /**
     * @notes 模型详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $params = (new ModelsValidate())->get()->goCheck('id');
        $result = AiModelsLogic::detail(intval($params['id']));
        if (!$result) {
            return $this->fail('模型不存在!');
        }
        return $this->data($result);
    }

    /**
     * @notes 模型创建
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new ModelsValidate())->post()->goCheck('add');
        $result = AiModelsLogic::add($params);
        if ($result === false) {
            return $this->fail(AiModelsLogic::getError());
        }
        return $this->success('创建成功', [], 1, 1);
    }

    /**
     * @notes 模型编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new ModelsValidate())->post()->goCheck('edit');
        $result = AiModelsLogic::edit($params);
        if ($result === false) {
            return $this->fail(AiModelsLogic::getError());
        }
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * @notes 模型删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new ModelsValidate())->post()->goCheck('id');
        $result = AiModelsLogic::del(intval($params['id']));
        if ($result === false) {
            return $this->fail(AiModelsLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 模型计费排序
     * @return Json
     * @author fzr
     */
    public function sort(): Json
    {
        $params = (new ModelsValidate())->post()->goCheck('sort');
        $result = AiModelsLogic::sort($params);
        if (!$result) {
            return $this->fail(AiModelsLogic::getError());
        }
        return $this->success('操作成功');
    }
}