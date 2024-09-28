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

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\VideoEnum;
use app\common\model\video\VideoRecord;
use app\common\service\FileService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 音乐记录列表
 */
class VideoRecordsLists extends BaseAdminDataLists
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

        if (isset($this->params['type']) && $this->params['type'] != '') {
            $where[] = ['r.type', '=', $this->params['type']];
        }

        if (isset($this->params['channel']) && $this->params['channel'] != '') {
            $where[] = ['r.channel', '=', $this->params['channel']];
        }

        if (isset($this->params['style_id']) && $this->params['style_id'] > 0) {
            $ids = VideoRecord::where("find_in_set('" . $this->params['style_id'] . "', style_id)")
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
        $model = new VideoRecord();
        $lists = $model->alias('r')
            ->join('user u', 'u.id = r.user_id')
            ->field(['r.id', 'r.prompt', 'r.tags', 'r.image', 'r.video_url', 'r.style_id',
                'r.channel','r.type','r.status', 'r.tokens', 'r.fail_reason','r.ip','r.create_time',
                'r.api_version','r.task_id','u.avatar', 'u.nickname', 'u.sn'])
            ->where($this->queryWhere())
            ->order('r.id desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->append(['status_desc', 'style_desc', 'type_desc'])
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['tokens'] = format_amount_zero($item['tokens']);
            $item['avatar'] = FileService::getFileUrl($item['avatar']);
            $item['channel'] = VideoEnum::getChannel($item['channel']);
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
        $model = new VideoRecord();
        return $model
            ->alias('r')
            ->join('user u', 'u.id = r.user_id')
            ->where($this->queryWhere())
            ->where($this->searchWhere)
            ->count();
    }
}