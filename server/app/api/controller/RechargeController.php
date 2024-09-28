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

namespace app\api\controller;

use app\api\lists\recharge\RechargeLists;
use app\api\lists\recharge\RecordLists;
use app\api\logic\RechargeLogic;
use app\api\validate\RechargeValidate;
use think\response\Json;

/**
 * 充值套餐管理
 */
class RechargeController extends BaseApiController
{
    /**
     * @notes 充值套餐列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new RechargeLists()));
    }

    /**
     * @notes 充值购买记录
     * @return Json
     * @author fzr
     */
    public function record(): Json
    {
        return $this->dataLists(new RecordLists());
    }

    /**
     * @notes 充值下单
     * @return Json
     * @author fzr
     */
    public function place(): Json
    {
        $params = (new RechargeValidate())->post()->goCheck('recharge', [
            'user_id'  => $this->userId,
            'terminal' => $this->userInfo['terminal'],
        ]);
        $result = RechargeLogic::place($params);
        if (false === $result) {
            return $this->fail(RechargeLogic::getError());
        }
        return $this->data($result);
    }
}