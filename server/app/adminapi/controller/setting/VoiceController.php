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
use app\adminapi\logic\setting\VoiceLogic;
use think\response\Json;

/**
 * 语音播报配置控制器类
 */
class VoiceController extends BaseAdminController
{
    /**
     * @notes 获取语音播报
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $result = VoiceLogic::detail();
        return $this->data($result);
    }

    /**
     * @notes 设置语音播报
     * @return Json
     * @author fzr
     */
    public function save(): Json
    {
        VoiceLogic::save($this->request->post());
        return $this->success('设置成功', [], 1, 1);
    }
}