<?php
namespace app\adminapi\controller\market;
use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\market\ActivityRewardLogic;

/**
 * 活动奖励控制器类
 * Class ActivityRewardController
 * @package app\adminapi\controller\market
 */
class ActivityRewardController extends BaseAdminController{

    /**
     * @notes 获取分享奖励设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 12:04
     */
    public function getShareSetting()
    {
        $result = (new ActivityRewardLogic())->getShareSetting();
        return $this->success('',$result);

    }

    /**
     * @notes 设置分享奖励设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 12:06
     */
    public function setShareSetting()
    {
        $params = $this->request->post();
        (new ActivityRewardLogic())->SetShareSetting($params);
        return $this->success('设置成功',[],1,1);
    }

    /**
     * @notes 获取邀请奖励设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 14:25
     */
    public function getInviteSetting()
    {
        $result = (new ActivityRewardLogic())->getInviteSetting();
        return $this->success('',$result);
    }

    /**
     * @notes 设置邀请奖励设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 14:34
     */
    public function setInviteSetting()
    {
        $params = $this->request->post();
        (new ActivityRewardLogic())->setInviteSetting($params);
        return $this->success('设置成功',[],1,1);
    }

    /**
     * @notes 获取签到设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 15:05
     */
    public function getSignSetting()
    {
        $result = (new ActivityRewardLogic())->getSignSetting();
        return $this->success('',$result);
    }


    /**
     * @notes 设置签到设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 15:05
     */
    public function setSignSetting()
    {
        $params = $this->request->post();
        (new ActivityRewardLogic())->setSignSetting($params);
        return $this->success('设置成功',[],1,1);
    }

    /**
     * @notes 获取工作分享设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 15:05
     */
    public function getWorkSetting()
    {
        $result = (new ActivityRewardLogic())->getWorkSetting();
        return $this->success('',$result);
    }

    /**
     * @notes 设置工作分享设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 15:12
     */
    public function setWorkSetting()
    {
        $params = $this->request->post();
        (new ActivityRewardLogic())->setWorkSetting($params);
        return $this->success('设置成功',[],1,1);
    }

    /**
     * @notes 获取机器人分享设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 15:05
     */
    public function getRobotSetting()
    {
        $result = (new ActivityRewardLogic())->getRobotSetting();
        return $this->success('',$result);
    }

    /**
     * @notes 获取机器人分享设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 15:05
     */
    public function setRobotSetting()
    {
        $params = $this->request->post();
        (new ActivityRewardLogic())->setRobotSetting($params);
        return $this->success('设置成功',[],1,1);
    }

    /**
     * @notes 广场设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/30 13:45
     */
    public function getSquareSetting()
    {
        $result = (new ActivityRewardLogic())->getSquareSetting();
        return $this->success('',$result);
    }


    /**
     * @notes 设置广场分享设置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/7/16 15:05
     */
    public function setSquareSetting()
    {
        $params = $this->request->post();
        (new ActivityRewardLogic())->setSquareSetting($params);
        return $this->success('设置成功',[],1,1);
    }
}