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

namespace app\adminapi\lists\setting;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\ChatEnum;
use app\common\enum\draw\DrawEnum;
use app\common\enum\MusicEnum;
use app\common\enum\PoolEnum;
use app\common\enum\SearchEnum;
use app\common\enum\VideoEnum;
use app\common\enum\VoiceEnum;
use app\common\lists\ListsExtendInterface;
use app\common\model\chat\KeyPool;
use app\common\model\chat\Models;
use app\common\service\FileService;

/**
 * Key池列表
 */
class KeyPoolLists extends BaseAdminDataLists implements ListsExtendInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author fzr
     */
    public function setSearch(): array
    {
        $type = intval($this->params['type'] ?? PoolEnum::TYPE_CHAT);

        $where = [];
        $where[] = ['type', '=', $type];

        if(isset($this->params['channel']) && $this->params['channel']){
            if (in_array($type, [PoolEnum::TYPE_CHAT, PoolEnum::TYPE_EMB])) {
                $where[] = ['model_id','=', $this->params['channel']];
            } else {
                $where[] = ['channel', '=', $this->params['channel']];
            }
        }

        if (isset($this->params['keyword']) && $this->params['keyword']) {
            $where[] = ['key', 'like', '%'.$this->params['keyword'].'%'];
        }

        if (isset($this->params['start_time']) && $this->params['start_time']){
            $where[] = ['create_time', '>=', strtotime($this->params['start_time'])];
        }

        if (isset($this->params['end_time']) && $this->params['end_time']){
            $where[] = ['create_time', '<=', strtotime($this->params['end_time'])];
        }

        if (isset($this->params['status']) && $this->params['status'] != ''){
            $where[] = ['status', '=', intval($this->params['status'])];
        }

        return $where;
    }

    /**
     * @notes 列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public function lists(): array
    {

        $model = new KeyPool();
        $lists = $model
            ->where($this->setSearch())
            ->field('id,type,channel,model_id,key,api,notice,remark,status,create_time,update_time')
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select()
            ->toArray();

        $modelIds = [];
        foreach ($lists as $item) {
            if (in_array($item['type'], [ChatEnum::MODEL_TYPE_CHAT, ChatEnum::MODEL_TYPE_EMB])) {
                $modelIds[] = $item['model_id'];
            }
        }

        $modelArray = [];
        if ($modelIds) {
            $type = intval($this->params['type'] ?? PoolEnum::TYPE_CHAT);
            $modelArray = (new Models())
                ->whereIn('id', array_unique($modelIds))
                ->where('type', $type)
                ->column('name', 'id');

        }

        foreach ($lists as &$item) {
            if (in_array($item['type'], [ChatEnum::MODEL_TYPE_CHAT, ChatEnum::MODEL_TYPE_EMB])) {
                $item['channel'] = $modelArray[$item['model_id']]??'-';
            } elseif ($item['type'] == PoolEnum::TYPE_MUSIC) {
                $item['channel'] = MusicEnum::getChannel($item['channel']);
            } elseif ($item['type'] == PoolEnum::TYPE_VIDEO) {
                $item['channel'] = VideoEnum::getChannel($item['channel']);
            } elseif ($item['type'] == PoolEnum::TYPE_DRAW) {
                $item['channel'] = DrawEnum::getAiModelName($item['channel']);
            } elseif ($item['type'] == PoolEnum::TYPE_SEARCH) {
                $item['channel'] = SearchEnum::getChannel($item['channel']);
            } else {
                $item['channel'] = VoiceEnum::getChannel($item['channel']);
            }
            $item['notice']  = $item['notice']?:'';
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
        $model = new KeyPool();
        return $model
            ->where($this->setSearch())
            ->count();
    }


    public function extend(): mixed
    {
        return [
            'template_url' => FileService::getFileUrl('resource/template/keyImportTemplate.xlsx')
        ];
    }
}