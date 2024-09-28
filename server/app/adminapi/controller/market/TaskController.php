<?php
namespace app\adminapi\controller\market;
use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\market\InviteLogLists;
use app\adminapi\lists\market\RobotLogLists;
use app\adminapi\lists\market\SignLogLists;
use app\adminapi\lists\market\ShareLogLists;
use app\adminapi\lists\market\WorksLogLits;

/**
 * 任务控制器类
 * Class TaskController
 * @package app\adminapi\controller\market
 */
class TaskController extends BaseAdminController
{

    /**
     * @notes 签到日志
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/26 11:10
     */
    public function signLogLists()
    {
        return $this->dataLists((new SignLogLists()));
    }

    /**
     * @notes 分享日志
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/26 11:31
     */
    public function shareLogLists()
    {
        return $this->dataLists(new ShareLogLists());
    }

    /**
     * @notes 邀请日志
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/26 11:48
     */
    public function inviteLogLists()
    {
        return $this->dataLists(new InviteLogLists());
    }

    /**
     * @notes 工作分享日志
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/26 14:49
     */
    public function workdsLogLists()
    {
        return $this->dataLists(new WorksLogLits());
    }

    /**
     * @notes 机器人分享记录列表
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/25 11:58
     */
    public function robotLogLists()
    {
        return $this->dataLists((new RobotLogLists()));
    }
}