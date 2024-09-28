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

namespace app\api\controller\chat;

use app\adminapi\logic\chat\ChatRecordLogic;
use app\api\controller\BaseApiController;
use app\api\logic\chat\ChatSampleLogic;
use think\response\Json;

/**
 * 问题示例控制器
 */
class ChatSampleController extends BaseApiController
{
    public array $notNeedLogin = ['samplesLists'];

    /**
     * @notes 问题示例列表
     * @return Json
     * @author fzr
     */
    public function samplesLists(): Json
    {
        $samplesLists = (new ChatSampleLogic())->samplesLists();
        return $this->data($samplesLists);
    }


}