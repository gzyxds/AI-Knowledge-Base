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

namespace app\adminapi\lists\market;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExtendInterface;
use app\common\model\kb\KbRobot;
use app\common\model\kb\KbRobotShareLog;
use app\common\service\FileService;

/**
 * 机器人分享记录列表
 */
class RobotLogLists extends BaseAdminDataLists implements ListsExtendInterface
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
        $lists = KbRobotShareLog::alias('KR')
            ->where($this->where())
            ->join('user U','KR.user_id = U.id')
            ->field('KR.*,U.avatar,U.nickname')
            ->order('KR.id desc')
            ->append(['channel_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()->toArray();
        $rotBotIds = array_column($lists,'robot_id');
        $rotBotLits = KbRobot::where(['id'=>$rotBotIds])->column('name','id');
        foreach ($lists as $key => $list){
            $lists[$key]['name'] = $rotBotLits[$list['robot_id']] ?? '';
            $lists[$key]['avatar'] = FileService::getFileUrl($list['avatar']);
            $lists[$key]['balance'] = format_amount_zero($list['balance']);
        }
        return $lists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public function count(): int
    {
        return KbRobotShareLog::alias('KR')
            ->where($this->where())
            ->join('user U','KR.user_id = U.id')
            ->field('KR.*,U.avatar,U.nickname')
            ->count();
            
    }

    public function where(): array
    {
        $where = [];
        if (isset($this->params['user_info']) && $this->params['user_info']) {
            $where[] = ['U.nickname|U.sn|U.mobile', 'like', '%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['KR.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['KR.create_time','<=',strtotime($this->params['end_time'])];
        }
        if (isset($this->params['source']) && $this->params['source'] != '') {
            $where[] = ['KR.source', '=', $this->params['source']];
        }
        return $where;
    }

    public function extend()
    {
        $shareNum = KbRobotShareLog::whereDay('create_time')->group('user_id')->count();
        $todayBalance = KbRobotShareLog::whereDay('create_time')->sum('balance');
        $totalBalance = KbRobotShareLog::sum('balance');
        return [
            'share_num'     => $shareNum,
            'today_balance' => $todayBalance,
            'total_balance' => $totalBalance
        ];
    }
}