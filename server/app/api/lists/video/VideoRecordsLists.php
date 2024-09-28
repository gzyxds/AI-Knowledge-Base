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

namespace app\api\lists\video;

use app\api\lists\BaseApiDataLists;
use app\common\model\video\VideoRecord;
use app\common\model\video\VideoRecordsCollect;
use app\common\model\video\VideoSquare;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 音乐记录列表
 */
class VideoRecordsLists extends BaseApiDataLists
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
        if (isset($this->params['status']) && $this->params['status'] > 0) {
            $where[] = ['status', '=', $this->params['status']];
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
        $lists = $model->field(['id','type','prompt','tags', 'image','scale','channel',
            'video_url', 'style_id', 'fail_reason', 'status', 'create_time'])
            ->where($this->queryWhere())
            ->where(['user_id' => $this->userId])
            ->order('id desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->append(['status_desc', 'style_desc', 'type_desc'])
            ->select()
            ->toArray();

        $shareIds = VideoSquare::where(['operate_id'=>$this->userId])
            ->column('records_id');

        foreach ($lists as &$item) {
            if (empty($item['style_id'])) {
                $item['style_id'] = [];
            } else {
                $item['style_id'] = explode(',', $item['style_id']);
            }
            $item['is_share'] = 0;
            if(in_array($item['id'],$shareIds)){
                $item['is_share'] = 1;
            }
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
            ->where($this->queryWhere())
            ->where(['user_id' => $this->userId])
            ->count();
    }
}