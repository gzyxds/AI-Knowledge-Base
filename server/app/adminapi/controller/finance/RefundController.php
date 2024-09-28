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

namespace app\adminapi\controller\finance;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\finance\RefundLogLists;
use app\adminapi\lists\finance\RefundRecordLists;
use app\adminapi\logic\finance\RefundLogic;
use think\response\Json;

/**
 * 退款控制器
 * Class RefundController
 * @package app\adminapi\controller\finance
 */
class RefundController extends BaseAdminController
{
    /**
     * @notes 退还统计
     * @return Json
     * @author 段誉
     * @date 2023/3/3 12:10
     */
    public function stat(): Json
    {
        $result = RefundLogic::stat();
        return $this->success('', $result);
    }

    /**
     * @notes 退款记录
     * @return Json
     * @author 段誉
     * @date 2023/3/1 9:47
     */
    public function record(): Json
    {
        return $this->dataLists(new RefundRecordLists());
    }

    /**
     * @notes 退款日志
     * @return Json
     * @author 段誉
     * @date 2023/3/1 9:47
     */
    public function log(): Json
    {
        $recordId = $this->request->get('record_id', 0);
        $result = RefundLogic::refundLog($recordId);
        return $this->success('', $result);
    }

    /**
     * @notes 重新退款
     * @return Json
     * @author 段誉
     * @date 2023/3/1 9:47
     */
    public function refundAgain(): Json
    {
        $recordId = $this->request->post('record_id', 0);
        $result = RefundLogic::refundAgain($recordId, $this->adminId);
        if(true !== $result) {
            return $this->fail(RefundLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }
}