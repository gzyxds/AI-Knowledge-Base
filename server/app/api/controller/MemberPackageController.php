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

use app\api\lists\MemberPackageCommentLists;
use app\api\logic\MemberLogic;
use app\api\logic\MemberPackageLogic;
use app\api\validate\MemberValidate;

/**
 * 获取会员套餐
 * Class MemberPackageController
 * @package app\api\controller
 */
class MemberPackageController extends BaseApiController
{


    /**
     * @notes 会员套餐
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/5/27 18:32
     */
    public function lists()
    {
        $lists = (new MemberPackageLogic())->lists($this->userId);
        return $this->success('', $lists);

    }


    /**
     * @notes 会员套餐评价列表
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/20 5:20 下午
     */
    public function commentLists()
    {
        return $this->dataLists(new MemberPackageCommentLists());
    }

    /**
     * @notes 购买
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/20 5:51 下午
     */
    public function buy()
    {
        $params = (new MemberValidate())->post()->goCheck('buy',['user_id'=>$this->userId,'terminal'=>$this->userInfo['terminal']]);
        $result = (new MemberLogic())->buy($params);
        if (false === $result) {
            return $this->fail(MemberLogic::getError());
        }
        return $this->success('',$result);
    }

    /**
     * @notes 最近三十条购买记录
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/25 7:32 下午
     */
    public function buyLog()
    {
        $result = (new MemberLogic())->buyLog($this->userId);
        return $this->success('',$result);
    }

}