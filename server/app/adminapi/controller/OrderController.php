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

namespace app\adminapi\controller;

use app\adminapi\lists\order\OrderLists;
use app\adminapi\logic\OrderLogic;
use app\adminapi\validate\IDMustValidate;
use think\response\Json;

/**
 * 订单管理
 */
class OrderController extends BaseAdminController
{
    /**
     * @notes 订单列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new OrderLists()));
    }

    /**
     * @notes 订单详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        (new IDMustValidate())->goCheck();
        $id = intval($this->request->get('id'));

        $detail = OrderLogic::detail($id);
        return $this->data($detail);
    }

    /**
     * @notes 发起退款
     * @return Json
     * @author fzr
     */
    public function refund(): Json
    {
        $params  = (new IDMustValidate())->post()->goCheck();
        $orderId = intval($params['id']);

        $result = OrderLogic::refund($orderId, $this->adminId);
        if($result === false) {
            return $this->fail(OrderLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 重新退款
     * @return Json
     * @author fzr
     */
    public function refundAgain(): Json
    {
        $params  = $this->request->post();
        $recordId = intval($params['record_id']??0);
        $orderId  = intval($params['order_id']??0);

        $result = OrderLogic::refundAgain($recordId, $orderId, $this->adminId);
        if($result === false) {
            return $this->fail(OrderLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }
}