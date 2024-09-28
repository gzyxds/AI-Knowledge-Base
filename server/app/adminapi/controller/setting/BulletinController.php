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
use app\adminapi\logic\setting\BulletinLogic;
use think\response\Json;

/**
 * 公告配置-控制器
 */
class BulletinController extends BaseAdminController
{
    /**
     * @notes 获取公告配置
     * @return Json
     * @author ljj
     * @date 2023/7/6 4:07 下午
     */
    public function detail(): Json
    {
        $result = BulletinLogic::detail();
        return $this->data($result);
    }

    /**
     * @notes 设置公告配置
     * @return Json
     * @author ljj
     * @date 2023/7/6 4:11 下午
     */
    public function save(): Json
    {
        $params = $this->request->post();
        BulletinLogic::save($params);
        return $this->success('设置成功', [], 1, 1);
    }
}