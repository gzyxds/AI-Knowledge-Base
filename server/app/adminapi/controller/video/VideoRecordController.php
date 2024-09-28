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
use app\adminapi\lists\video\VideoRecordsLists;
use app\adminapi\logic\video\VideoRecordLogic;
use think\response\Json;

/**
 * 视频管理
 */
class VideoRecordController extends BaseAdminController
{
    /**
     * @notes 音乐列表
     * @return Json
     * @author mjf
     * @date 2024/5/30 15:11
     */
    public function lists(): Json
    {
        return $this->dataLists((new VideoRecordsLists()));
    }

    /**
     * @notes 记录删除
     * @return Json
     * @author mjf
     */
    public function del(): Json
    {
        $params = $this->request->post();
        VideoRecordLogic::del($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 下拉选项
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2024/5/30 16:36
     */
    public function options(): Json
    {
        $result = VideoRecordLogic::options();
        return $this->data($result);
    }

}