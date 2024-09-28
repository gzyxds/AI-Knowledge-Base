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

namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\setting\KeyRuleLists;
use app\adminapi\logic\setting\KeyRuleLogic;
use app\adminapi\validate\setting\KeyRuleValidate;
use app\common\service\ConfigService;
use think\response\Json;

/**
 * Key规则管理
 */
class KeyRuleController extends BaseAdminController
{
    /**
     * @notes 规则列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new KeyRuleLists()));
    }

    /**
     * @notes 规则详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $params = (new KeyRuleValidate())->get()->goCheck('id');
        $detail = KeyRuleLogic::detail(intval($params['id']));
        return $this->data($detail);
    }

    /**
     * @notes 规则新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new KeyRuleValidate())->post()->goCheck('add');
        $result = KeyRuleLogic::add($params);
        if ($result === false) {
            return $this->fail(KeyRuleLogic::getError());
        }
        return $this->success('添加成功');
    }

    /**
     * @notes 规则编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new KeyRuleValidate())->post()->goCheck();
        $result = KeyRuleLogic::edit($params);
        if ($result === false) {
            return $this->fail(KeyRuleLogic::getError());
        }
        return $this->success('编辑成功');
    }

    /**
     * @notes 规则删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new KeyRuleValidate())->post()->goCheck('id');
        $result = KeyRuleLogic::del(intval($params['id']));
        if ($result === false) {
            return $this->fail(KeyRuleLogic::getError());
        }
        return $this->success('删除成功');
    }

    /**
     * @notes 规则状态
     * @return Json
     * @author fzr
     */
    public function status(): Json
    {
        $params = (new KeyRuleValidate())->post()->goCheck('id');
        $result = KeyRuleLogic::status(intval($params['id']));
        if ($result === false) {
            return $this->fail(KeyRuleLogic::getError());
        }
        return $this->success(KeyRuleLogic::getError());
    }

    /**
     * @notes 规则配置详情
     * @return Json
     * @author fzr
     */
    public function getConfig(): Json
    {
        $keyAutoDown = intval(ConfigService::get('key_pool','key_auto_down', 1));
        $results = ['key_auto_down'=>$keyAutoDown];
        return $this->success('', $results);
    }

    /**
     * @notes 规则配置保存
     * @return Json
     * @author fzr
     */
    public function setConfig(): Json
    {
        $status = $this->request->post('key_auto_down', 0);
        ConfigService::set('key_pool','key_auto_down', $status);
        return $this->success('设置成功');
    }
}