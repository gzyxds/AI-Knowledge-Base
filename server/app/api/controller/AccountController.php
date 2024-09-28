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

use app\api\lists\AccountLogLists;
use app\api\logic\AccountLogic;
use think\response\Json;

/**
 * 账户明细管理
 */
class AccountController extends BaseApiController
{
    /**
     * @notes 余额明细列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new AccountLogLists()));
    }

    /**
     * @notes 余额明细详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $id = intval($this->request->get('id'));
        $detail = AccountLogic::detail($id);
        return $this->data($detail);
    }
}