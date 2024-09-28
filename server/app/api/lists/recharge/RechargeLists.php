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

namespace app\api\lists\recharge;

use app\api\lists\BaseApiDataLists;
use app\common\lists\ListsExtendInterface;
use app\common\model\recharge\RechargePackage;
use app\common\model\user\User;

class RechargeLists extends BaseApiDataLists implements ListsExtendInterface
{
    /**
     * @notes 列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public function lists(): array
    {
        $model = new RechargePackage();
        return $model
            ->withoutField('sort,status,create_time,update_time,delete_time')
            ->where(['status'=>1])
            ->order('sort desc, id desc')
            ->select()
            ->toArray();
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public function count(): int
    {
        $model = new RechargePackage();
        return $model->where(['status'=>1])->count();
    }

    /**
     * @notes 扩展
     * @return array
     * @author fzr
     */
    public function extend(): array
    {
        $modelUser = new User();
        $user = $modelUser
            ->field(['id,balance,robot_num,video_num'])
            ->where(['id'=>$this->userId])
            ->findOrEmpty()
            ->toArray();

        return [
            'balance'   => format_amount_zero($user['balance']),
            'robot_num' => $user['robot_num'],
            'video_num' => $user['video_num'],
        ]??[];
    }
}