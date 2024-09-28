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

namespace app\adminapi\controller\video;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\video\VideoConfigLogic;
use think\response\Json;

/**
 * Class VideoConfigController
 * @package app\adminapi\controller\video
 */
class VideoConfigController extends BaseAdminController
{
    /**
     * @notes 配置详情
     * @return Json
     * @author mjf
     * @date 2024/5/27 10:49
     */
    public function detail(): Json
    {
        $result = VideoConfigLogic::detail();
        return $this->data($result);
    }

    /**
     * @notes 保存配置
     * @return Json
     * @author mjf
     * @date 2024/5/27 10:49
     */
    public function save(): Json
    {
        $result = VideoConfigLogic::save($this->request->post());
        if ($result === false) {
            return $this->fail(VideoConfigLogic::getError());
        }
        return $this->success('保存成功', [], 1, 1);
    }
}