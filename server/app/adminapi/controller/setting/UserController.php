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
use app\adminapi\logic\setting\UserLogic;
use app\adminapi\validate\setting\UserConfigValidate;
use think\response\Json;

/**
 * 设置-用户设置控制器
 */
class UserController extends BaseAdminController
{
    /**
     * @notes 获取用户设置
     * @return Json
     * @author 段誉
     * @date 2022/3/29 10:08
     */
    public function getConfig(): Json
    {
        $result = (new UserLogic())->getConfig();
        return $this->data($result);
    }

    /**
     * @notes 设置用户设置
     * @return Json
     * @author 段誉
     * @date 2022/3/29 10:08
     */
    public function setConfig(): Json
    {
        $params = (new UserConfigValidate())->post()->goCheck('user');
        (new UserLogic())->setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 获取注册配置
     * @return Json
     * @author 段誉
     * @date 2022/3/29 10:08
     */
    public function getRegisterConfig(): Json
    {
        $result = (new UserLogic())->getRegisterConfig();
        return $this->data($result);
    }

    /**
     * @notes 设置注册配置
     * @return Json
     * @author 段誉
     * @date 2022/3/29 10:08
     */
    public function setRegisterConfig(): Json
    {
        $params = (new UserConfigValidate())->post()->goCheck('register');
        (new UserLogic())->setRegisterConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }
}