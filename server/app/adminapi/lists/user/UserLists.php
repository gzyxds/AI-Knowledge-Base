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
namespace app\adminapi\lists\user;

use app\adminapi\lists\BaseAdminDataLists;
use app\api\logic\MemberPackageLogic;
use app\common\enum\PayEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\lists\ListsExcelInterface;
use app\common\logic\UserMemberLogic;
use app\common\model\kb\KbKnow;
use app\common\model\kb\KbRobot;
use app\common\model\Order;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;

/**
 * 用户列表
 */
class UserLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author 段誉
     * @date 2022/9/22 15:50
     */
    public function setSearch(): array
    {
        $allowSearch = ['keyword', 'channel', 'create_time_start', 'create_time_end'];
        return array_intersect(array_keys($this->params), $allowSearch);
    }

    /**
     * @notes 分组条件
     * @return array|string
     */
    public function where(): array|string
    {
        $where = [];
        if (isset($this->params['group_id']) && $this->params['group_id']) {
            $groupId = $this->params['group_id'];
            $where[] = ['group_ids', 'find in set', $groupId];
        }
        if(isset($this->params['is_distribution']) && '' != $this->params['is_distribution']){
            $where[] = ['is_distribution','=',$this->params['is_distribution']];
        }
        return $where;
    }

    /**
     * @notes 获取用户列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/22 15:50
     */
    public function lists(): array
    {
        $lists = (new User())
            ->field([
                'id,sn,nickname,sex,avatar,account,mobile,email,channel,is_blacklist,create_time',
                'balance,robot_num,video_num,total_chat,inviter_id,total_space'
            ])
            ->withSearch($this->setSearch(), $this->params)
            ->where($this->where())
            ->append(['inviter_name'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select()
            ->toArray();
//        $firstLeader = array_column($lists,'first_leader');
//        $firstLeaderLists = User::where(['id'=>$firstLeader])->column('nickname','id');
        $modelKbRobot = new KbRobot();
        foreach ($lists as &$item) {
            // 机器人统计
            $useRobotNum = $modelKbRobot->where(['user_id'=>$item['id']])->count();
            $item['robot_num'] = $useRobotNum . '/'. ($useRobotNum + $item['robot_num']);

            // 数字人余额
            $item['video_num'] = format_amount_zero($item['video_num']).'分钟';
            $item['balance'] = format_amount_zero($item['balance']);

            // 来源渠道
            $item['channel'] = UserTerminalEnum::getTerminalDesc($item['channel']);
            $userMember = UserMemberLogic::getUserMember($item['id'],true);
            $item['package_name'] = '-';
            $item['package_time'] = '-';
            $item['package_is_overdue'] = false;
            if($userMember){
                $item['package_name'] = $userMember['name'];
                $item['package_time'] = 1 == $userMember['is_perpetual'] ? '永久套餐' : date('Y-m-d H:i:s',$userMember['member_end_time']);
                $item['package_is_overdue'] = 1 == $userMember['is_overdue'];
            }
//            $item['first_leader_nickname'] = $firstLeaderLists[$item['first_leader']] ?? '';
        }

        return $lists;
    }

    /**
     * @notes 获取数量
     * @return int
     * @throws @\think\db\exception\DbException
     * @author 段誉
     * @date 2022/9/22 15:51
     */
    public function count(): int
    {
        return (new User())
            ->withSearch($this->setSearch(), $this->params)
            ->where($this->where())
            ->count();
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author 段誉
     * @date 2022/11/24 16:17
     */
    public function setFileName(): string
    {
        return '用户列表';
    }

    /**
     * @notes 导出字段
     * @return string[]
     * @author 段誉
     * @date 2022/11/24 16:17
     */
    public function setExcelFields(): array
    {
        return [
            'sn'          => '用户编号',
            'nickname'    => '用户昵称',
            'account'     => '账号',
            'mobile'      => '手机号码',
            'channel'     => '注册来源',
            'create_time' => '注册时间'
        ]??[];
    }
}