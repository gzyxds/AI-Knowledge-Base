<?php
namespace app\adminapi\lists\market;
use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExtendInterface;
use app\common\model\task\TaskSign;
use app\common\service\FileService;

class SignLogLists extends BaseAdminDataLists implements ListsExtendInterface{

    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2024/2/27 10:54 上午
     */
    public function where()
    {
        $where = [];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname|u.mobile','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['ts.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['ts.create_time','<=',strtotime($this->params['end_time'])];
        }

        return $where;
    }

    /**
     * @notes 签到记录列表
     * @return array
     * @author ljj
     * @date 2024/2/27 10:54 上午
     */
    public function lists(): array
    {
        $lists = TaskSign::alias('ts')
            ->join('user u', 'u.id = ts.user_id')
            ->field('u.nickname,u.avatar,ts.id,ts.user_id,ts.channel,ts.balance,ts.create_time')
            ->where($this->where())
            ->append(['channel_desc'])
            ->order('id','desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()->toArray();
        foreach ($lists as $key => $list){
            $lists[$key]['avatar'] = FileService::getFileUrl($list['avatar']);
        }
        return $lists;
    }

    /**
     * @notes 签到记录数量
     * @return int
     * @author ljj
     * @date 2024/2/27 10:58 上午
     */
    public function count(): int
    {
        return TaskSign::alias('ts')
            ->join('user u', 'u.id = ts.user_id')
            ->where($this->where())
            ->count();
    }

    /**
     * @notes 统计数据
     * @return array
     * @author ljj
     * @date 2024/2/27 10:59 上午
     */
    public function extend()
    {
        return [
            'today_sign_num' => TaskSign::alias('ts')
                ->join('user u', 'u.id = ts.user_id')
                ->whereDay('ts.create_time')
                ->where($this->where())
                ->count(),
            'today_balance' => TaskSign::alias('ts')
                ->join('user u', 'u.id = ts.user_id')
                ->whereDay('ts.create_time')
                ->where($this->where())
                ->sum('ts.balance'),
            'total_balance' => TaskSign::alias('ts')
                ->join('user u', 'u.id = ts.user_id')
                ->where($this->where())
                ->sum('ts.balance'),
        ];
    }

}