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

namespace app\adminapi\controller\recharge;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\recharge\RechargeOrderLists;
use app\adminapi\logic\recharge\RechargeOrderLogic;
use app\adminapi\validate\IDMustValidate;
use think\response\Json;

/**
 * 充值订单管理
 */
class OrderController extends BaseAdminController
{
    /**
     * @notes 充值记录列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists(new RechargeOrderLists());
    }

    /**
     * @notes 充值记录详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        (new IDMustValidate())->get()->goCheck();
        $id = intval($this->request->get('id'));

        $detail = RechargeOrderLogic::detail($id);
        return $this->data($detail);
    }

    /**
     * @notes 充值订单退款
     * @return Json
     * @author fzr
     */
    public function refund(): Json
    {
        $id = intval($this->request->post('id'));
        $result = RechargeOrderLogic::refund($id, $this->adminId);
        if(true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功', [], 1, 1);
    }
}