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

namespace app\api\controller\kb;

use app\api\controller\BaseApiController;
use app\api\lists\kb\KbRobotLists;
use app\api\logic\kb\KbRobotLogic;
use app\api\validate\kb\KbRobotValidate;
use app\common\logic\BaseLogic;
use Exception;
use think\db\exception\DbException;
use think\response\Json;

/**
 * 机器人管理
 */
class RobotController extends BaseApiController
{
    public array $notNeedLogin = ['lists'];

    /**
     * @notes 机器人列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new KbRobotLists()));
    }

    /**
     * @notes 机器人详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $params = (new KbRobotValidate())->get()->goCheck('id');
        try {
            $detail = KbRobotLogic::detail(intval($params['id']), $this->userId);
            return $this->data($detail);
        } catch (Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * @notes 机器人新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $post = $this->request->post();
        $results = KbRobotLogic::add($post, $this->userId);
        if ($results === false) {
            return $this->fail(KbRobotLogic::getError());
        }
        return $this->success('创建成功', $results, 1, 1);
    }

    /**
     * @notes 机器人编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new KbRobotValidate())->post()->goCheck('edit');
        $results = KbRobotLogic::edit($params, $this->userId);
        if ($results === false) {
            return $this->fail(KbRobotLogic::getError());
        }
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * @notes 机器人删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new KbRobotValidate())->post()->goCheck('id');
        $results = KbRobotLogic::del(intval($params['id']), $this->userId);
        if ($results === false) {
            return $this->fail(KbRobotLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 分享列表
     * @return Json
     * @author cjhao
     * @date 2024/7/25 11:26
     */
    public function categoryLists(){
        $results = KbRobotLogic::categoryLists();
        return $this->success('', $results);
    }


    /**
     * @notes 机器人分享
     * @return Json
     * @author cjhao
     * @date 2024/7/25 11:22
     */
    public function share()
    {
        $params = $this->request->post();
        $result = KbRobotLogic::share($params, $this->userInfo);
        if (true !== $result) {
            return $this->fail($result);
        }
        $tips = BaseLogic::getReturnData() ?: '分享成功';
        return $this->success($tips, [], 1, 1);

    }

    /**
     * @notes 取消分享
     * @return Json
     * @author cjhao
     * @date 2024/7/26 16:36
     */
    public function cancelShare()
    {
        $params = $this->request->post();
        $result = KbRobotLogic::cancelShare($params,$this->userId);
        if(false === $result){
            return $this->fail(KbRobotLogic::getError());
        }
        return $this->success('取消成功', [], 1, 1);
//        if ($results === false) {
//            return $this->fail(KbRobotLogic::getError());
//        }
    }
}