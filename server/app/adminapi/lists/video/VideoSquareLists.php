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

namespace app\adminapi\lists\video;

use app\api\lists\BaseApiDataLists;
use app\common\model\user\User;
use app\common\model\video\VideoSquare;


class VideoSquareLists extends BaseApiDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/8/31 4:07 下午
     */
    public function where()
    {
        $where = [];
        if(isset($this->params['user_info']) && $this->params['user_info'] != ''){
            $userIds = User::where('sn|nickname','like','%'.$this->params['user_info'].'%')->column('id');
            empty($userIds) && $userIds = [];
            $where[] = ['vs.user_id','in',implode(',',$userIds)];
        }
        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['vs.lyric|title', 'like', '%'.$this->params['keyword'].'%'];
        }
        if (isset($this->params['category_id']) && $this->params['category_id'] != '') {
            $where[] = ['vs.category_id', '=', $this->params['category_id']];
        }
        if (isset($this->params['verify_status']) && $this->params['verify_status'] != '') {
            $where[] = ['vs.verify_status', '=', $this->params['verify_status']];
        }
        if (isset($this->params['is_show']) && $this->params['is_show'] != '') {
            $where[] = ['vs.is_show', '=', $this->params['is_show']];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['vs.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['vs.create_time','<=',strtotime($this->params['end_time'])];
        }
        if (isset($this->params['source']) && $this->params['source'] != '') {
            $where[] = ['vs.source', '=', $this->params['source']];
        }
        return $where;
    }
    /**
     * @notes 广场列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/8/31 4:09 下午
     */
    public function lists(): array
    {

        $lists = VideoSquare::alias('vs')
            ->leftjoin('video_record vr', 'vr.id = vs.records_id')
            ->field(
                'vs.id,vs.source,vs.operate_id,vs.prompt,
                vs.video_url,vs.image,vs.create_time,vs.category_id,vs.verify_status,vs.is_show'
            )
            ->append(['category_name','user_info','verify_status_desc','source_desc'])
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['vs.id' => 'desc'])
            ->select()
            ->toArray();


        return $lists;
    }

    /**
     * @notes 广场数量
     * @return int
     * @author ljj
     * @date 2023/8/31 4:09 下午
     */
    public function count(): int
    {
        return VideoSquare::alias('vs')
            ->leftjoin('video_record vr', 'vr.id = vs.records_id')
            ->where($this->where())
            ->count();
    }
}