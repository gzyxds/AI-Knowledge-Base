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

namespace app\adminapi\lists\music;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\MusicEnum;
use app\common\model\music\MusicRecord;
use app\common\service\FileService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 音乐记录列表
 */
class MusicRecordsLists extends BaseAdminDataLists
{

    /**
     * @notes 搜索
     * @return array
     * @author mjf
     * @date 2024/5/30 10:02
     */
    public function queryWhere(): array
    {
        $where = [];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname|u.mobile', 'like', '%' . $this->params['user_info'] . '%'];
        }
        if (isset($this->params['prompt']) && $this->params['prompt'] != '') {
            $where[] = ['r.prompt', 'like', '%' . $this->params['prompt'] . '%'];
        }
        if (isset($this->params['status']) && $this->params['status'] >= 0) {
            $where[] = ['r.status', '=', $this->params['status']];
        }
        if (isset($this->params['channel']) && $this->params['channel'] >= 0) {
            $where[] = ['r.channel', '=', $this->params['channel']];
        }
        if (isset($this->params['style_id']) && $this->params['style_id'] > 0) {
            $ids = MusicRecord::where("find_in_set('".$this->params['style_id']."', style_id)")
                ->column('id');
            $where[] = ['r.id', 'in', $ids];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['r.create_time', '>=', strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['r.create_time', '<=', strtotime($this->params['end_time'])];
        }
        return $where;
    }

    /**
     * @notes 获取列表
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author mjf
     * @date 2024/5/29 17:40
     */
    public function lists(): array
    {
        $model = new MusicRecord();
        $lists = $model->alias('r')
            ->join('user u', 'u.id = r.user_id')
            ->field(['r.id','r.title','r.prompt','r.tags','r.image_url','r.image_large_url','r.channel',
                'r.video_url', 'r.audio_url', 'r.lyric', 'r.custom_mode', 'r.make_instrumental', 'r.mv',
                'r.duration', 'r.status', 'r.tokens', 'r.fail_reason', 'r.create_time','r.style_id','r.ip',
                'r.task_id','r.clips_id','u.avatar', 'u.nickname', 'u.sn'])
            ->where($this->queryWhere())
            ->order('r.id desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->append(['status_desc', 'style_desc'])
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['tokens'] = format_amount_zero($item['tokens']);
            $item['avatar'] = FileService::getFileUrl($item['avatar']);
            $item['mv'] = MusicEnum::getVersion($item['mv']);
            $item['channel'] = MusicEnum::getChannel($item['channel']);
        }

        return $lists;
    }

    /**
     * @notes 获取数量
     * @return int
     * @throws DbException
     * @author mjf
     * @date 2024/5/29 17:40
     */
    public function count(): int
    {
        $model = new MusicRecord();
        return $model
            ->alias('r')
            ->join('user u', 'u.id = r.user_id')
            ->where($this->queryWhere())
            ->where($this->searchWhere)
            ->count();
    }
}