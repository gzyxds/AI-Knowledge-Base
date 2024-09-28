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

namespace app\adminapi\controller\notice;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\notice\EmailLogic;
use think\response\Json;

/**
 * 邮件配置-控制器
 */
class EmailController extends BaseAdminController
{
    /**
     * @notes 获取邮箱配置
     * @return Json
     * @author ljj
     * @date 2023/7/20 2:22 下午
     */
    public function detail(): Json
    {
        $result = EmailLogic::getConfig();
        return $this->data($result);
    }

    /**
     * @notes 设置邮箱配置
     * @return Json
     * @author ljj
     * @date 2023/7/20 2:31 下午
     */
    public function save(): Json
    {
        $params = $this->request->post();
        EmailLogic::setConfig($params);
        return $this->success('操作成功',[],1,1);
    }
}