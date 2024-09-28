<?php
namespace app\adminapi\lists\market;
use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExtendInterface;
use app\common\model\draw\DrawRecords;
use app\common\model\music\MusicRecord;
use app\common\model\video\VideoRecord;
use app\common\model\video\VideoRecordsCollect;
use app\common\model\WorksShareLog;
use app\common\service\FileService;

class WorksLogLits extends BaseAdminDataLists implements ListsExtendInterface{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2024/2/27 10:54 上午
     */
    public function where()
    {
        $where = [];
        if(isset($this->params['type']) && $this->params['type']){
            $where[] = ['ws.type','=',$this->params['type']];
        }
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname|u.mobile','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['ws.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['ws.create_time','<=',strtotime($this->params['end_time'])];
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
        $lists = WorksShareLog::alias('ws')
            ->join('user u', 'u.id = ws.user_id')
            ->field('u.nickname,ws.work_id,u.avatar,ws.type,ws.id,ws.user_id,ws.channel,ws.balance,ws.create_time')
            ->where($this->where())
            ->append(['channel_desc'])
            ->order('id','desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()->toArray();
        $ids =  array_column($lists,'work_id');
        $videoLists = VideoRecord::where(['id'=>$ids])->column('video_url','id');
        $musicLists = MusicRecord::where(['id'=>$ids])->column('image_url','id');
        $drawLists  = DrawRecords::where(['id'=>$ids])->column('image_url','id');
        foreach ($lists as $key => $list){
            $lists[$key]['avatar'] = FileService::getFileUrl($list['avatar']);
            $lists[$key]['work_url'] = '';
            switch ($list['type']){
                case 1:
                    $worksUrl = $drawLists[$list['work_id']] ?? '';
                    break;
                case 2:
                    $worksUrl = $musicLists[$list['work_id']] ?? '';
                    break;
                case 3:
                    $worksUrl = $videoLists[$list['work_id']] ?? '';
                    break;
            }
            if($worksUrl){
                $lists[$key]['work_url'] = FileService::getFileUrl($worksUrl);
            }
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
        return WorksShareLog::alias('ws')
            ->join('user u', 'u.id = ws.user_id')
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
            'today_share_num' => WorksShareLog::alias('ws')
                ->join('user u', 'u.id = ws.user_id')
                ->whereDay('ws.create_time')
                ->where($this->where())
                ->count(),
            'today_balance' => WorksShareLog::alias('ws')
                ->join('user u', 'u.id = ws.user_id')
                ->whereDay('ws.create_time')
                ->where($this->where())
                ->sum('ws.balance'),
            'total_balance' => WorksShareLog::alias('ws')
                ->join('user u', 'u.id = ws.user_id')
                ->where($this->where())
                ->sum('ws.balance'),
        ];
    }

}