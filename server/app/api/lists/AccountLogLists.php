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

namespace app\api\lists;

use app\common\enum\user\AccountLogEnum;
use app\common\lists\ListsExtendInterface;
use app\common\model\kb\KbRobot;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use JetBrains\PhpStorm\Pure;

/**
 * 余额明细列表
 */
class AccountLogLists extends BaseApiDataLists implements ListsExtendInterface
{
    #[Pure]
    public function where(): array
    {
        $type = intval($this->params['type']??'');
        $changeType = intval($this->params['change_type']??'');
        $where[] = ['user_id', '=', $this->userId];
        if($type){
            $where[] = ['change_type', 'in', AccountLogEnum::getChangeTypeCodes($type)];
        }
        if($changeType){
            $where[] = ['change_type', '=', $changeType];
        }
        if(isset($this->params['action']) && $this->params['action']){
            $where[] = ['action','=',$this->params['action']];
        }
        return $where;
    }

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
        $model = new UserAccountLog();
        $lists = $model
            ->field('id,sn,action,robot_name,change_type,change_amount,left_amount,create_time')
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            if (!$item['robot_name'] && $item['change_type'] == AccountLogEnum::UM_DEC_CHAT) {
                $item['robot_name'] = '普通对话';
            }

            $item['change_amount'] = format_amount_zero($item['change_amount']);
            $item['left_amount']   = format_amount_zero($item['left_amount']);
            $item['change_type']   = AccountLogEnum::getChangeTypeDesc($item['change_type']);
        }

        return $lists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     */
    public function count(): int
    {
        $model = new UserAccountLog();
        return $model->where($this->where())->count();
    }

    public function extend(): array
    {
        $user = (new User())
            ->field(['balance', 'video_num', 'robot_num'])
            ->where(['id'=>$this->userId])
            ->findOrEmpty()
            ->toArray();

        $user['balance'] = format_amount_zero($user['balance']);
        return $user;
    }
}