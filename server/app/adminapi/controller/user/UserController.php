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

namespace app\adminapi\controller\user;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\user\UserLists;
use app\adminapi\logic\user\UserLogic;
use app\adminapi\validate\user\AdjustAccountValidate;
use app\adminapi\validate\user\UserValidate;
use app\api\logic\MemberLogic;
use think\response\Json;

/**
 * 用户控制器
 */
class UserController extends BaseAdminController
{
    /**
     * @notes 用户列表
     * @return Json
     * @author 段誉
     * @date 2022/9/22 16:16
     */
    public function lists(): Json
    {
        return $this->dataLists(new UserLists());
    }

    /**
     * @notes 获取用户详情
     * @return Json
     * @author 段誉
     * @date 2022/9/22 16:34
     */
    public function detail(): Json
    {
        $params = (new UserValidate())->goCheck('detail');
        $detail = UserLogic::detail($params['id']);

        return $this->success('', $detail);
    }

    /**
     * @notes 编辑用户
     * @return Json
     * @author 段誉
     * @date 2022/9/22 16:34
     */
    public function edit(): Json
    {
        $params = (new UserValidate())->post()->goCheck('setInfo');
        UserLogic::edit($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 创建用户
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new UserValidate())->post()->goCheck('create');
        $result = UserLogic::createUser($params);
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('创建成功', [], 1, 1);
    }

    /**
     * @notes 重置密码
     * @return Json
     * @author fzr
     */
    public function rePassword(): Json
    {
        $params = (new UserValidate())->post()->goCheck('resetPwd');
        $result = UserLogic::resetPwd(intval($params['id']), $params['password']);
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('重置成功', [], 1, 1);
    }

    /**
     * @notes 加入黑名单
     * @return Json
     * @author fzr
     */
    public function blacklist(): Json
    {
        $params = (new UserValidate())->post()->goCheck('detail');
        $result = UserLogic::blacklist(intval($params['id']));
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success(UserLogic::getError(), [], 1, 1);
    }

    /**
     * @notes 设置用户分组
     * @return Json
     * @author fzr
     */
    public function setGroup(): Json
    {
        $ids      = $this->request->post('ids', []);
        $groupIds = $this->request->post('group_ids', []);

        $result = UserLogic::setGroup($ids, $groupIds);
        if ($result === true) {
            return $this->success('设置成功', [], 1, 1);
        }
        return $this->fail(UserLogic::getError());
    }

    /**
     * @notes 调整账户
     * @return Json
     * @author fzr
     */
    public function adjustAccount(): Json
    {
        $params = (new AdjustAccountValidate())->post()->goCheck();
        $result = UserLogic::adjustAccount($params, $this->adminId);
        if ($result === false) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('调整成功', [], 1, 1);
    }

    /**
     * @notes 调整会员到期时间
     * @return Json
     * @author ljj
     * @date 2023/4/14 4:12 下午
     */
    public function adjustMember(): Json
    {
        $params = (new UserValidate())->post()->goCheck('adjustMember',['admin_id'=>$this->adminId]);
        $result = UserLogic::adjustMember($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 调整邀请人
     * @return Json
     * @author ljj
     * @date 2024/1/10 11:49 上午
     */
    public function adjustLeader(): Json
    {
        $params = (new UserValidate())->post()->goCheck('adjustLeader',['admin_id'=>$this->adminId]);
        $result = UserLogic::adjustLeader($params);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 调整知识库空间
     * @return Json
     * @author fzr
     */
    public function adjustSpace(): Json
    {
        $params = (new UserValidate())->post()->goCheck('adjustSpace',['admin_id'=>$this->adminId]);
        $result = UserLogic::adjustSpace($params);
        if (true !== $result) {
            return $this->fail(UserLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 用户购买记录
     * @return Json
     * @author ljj
     * @date 2023/4/25 7:32 下午
     */
    public function buyLog(): Json
    {
        $userId = $this->request->get('user_id', '');
        $result = (new MemberLogic())->buyLog($userId);
        return $this->success('',$result);
    }

    /**
     * @notes 账号注销
     * @return Json
     * @author fzr
     */
    public function cancelled(): Json
    {
        $userId = $this->request->post('user_id', '');
        $result = UserLogic::cancelled($userId);
        if (true !== $result) {
            return $this->fail($result);
        }
        return $this->success('注销成功', [], 1, 1);
    }
}