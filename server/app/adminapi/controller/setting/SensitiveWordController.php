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
use app\adminapi\lists\setting\SensitiveWordLists;
use app\adminapi\logic\setting\SensitiveWordLogic;
use app\adminapi\validate\setting\SensitiveWordValidate;
use think\response\Json;

/**
 * 敏感词库控制器类
 */
class SensitiveWordController extends BaseAdminController
{
    /**
     * @notes 敏感词列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new SensitiveWordLists()));
    }

    /**
     * @notes 敏感词详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $params = (new SensitiveWordValidate())->get()->goCheck('id');
        $result = SensitiveWordLogic::detail(intval($params['id']));
        return $this->data($result);
    }

    /**
     * @notes 敏感词新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new SensitiveWordValidate())->post()->goCheck('add');
        $result = SensitiveWordLogic::add($params);
        if ($result === false) {
            return $this->fail(SensitiveWordLogic::getError());
        }
        return $this->success('新增成功', [], 1, 1);
    }

    /**
     * @notes 敏感词编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new SensitiveWordValidate())->post()->goCheck('edit');
        $result = SensitiveWordLogic::edit($params);
        if ($result === false) {
            return $this->fail(SensitiveWordLogic::getError());
        }
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * @notes 敏感词删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new SensitiveWordValidate())->post()->goCheck('id');
        $result = SensitiveWordLogic::del(intval($params['id']));
        if ($result === false) {
            return $this->fail(SensitiveWordLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 敏感词状态
     * @return Json
     * @author fzr
     */
    public function status(): Json
    {
        $params = (new SensitiveWordValidate())->post()->goCheck('id');
        $result = SensitiveWordLogic::status(intval($params['id']));
        if ($result === false) {
            return $this->fail(SensitiveWordLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 配置详情
     * @return Json
     * @author fzr
     */
    public function getConfig(): Json
    {
        $result = SensitiveWordLogic::getConfig();
        return $this->data($result);
    }

    /**
     * @notes 配置保存
     * @return Json
     * @author fzr
     */
    public function setConfig(): Json
    {
        $params = (new SensitiveWordValidate())->post()->goCheck('setConfig');
        SensitiveWordLogic::setConfig($params);
        return $this->success('设置成功', [], 1, 1);
    }
}