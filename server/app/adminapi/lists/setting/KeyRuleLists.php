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
use app\common\enum\PoolEnum;
use app\common\enum\VoiceEnum;
use app\common\lists\ListsExcelInterface;
use app\common\model\chat\KeyRule;
use app\common\model\chat\Models;

class KeyRuleLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜搜条件
     * @return array
     * @author fzr
     */
    public function setSearch(): array
    {
        $type = $this->params['type'] ?? PoolEnum::TYPE_CHAT;

        $where[] = ['type', '=', $type];
        if(isset($this->params['channel']) && $this->params['channel']){
            if (in_array($type, [PoolEnum::TYPE_CHAT, PoolEnum::TYPE_EMB])) {
                $where[] = ['model_id','=', $this->params['channel']];
            } else {
                $where[] = ['channel', '=', trim($this->params['channel'])];
            }
        }

        if (isset($this->params['status']) && $this->params['status'] != '') {
            $where[] = ['status', '=', intval($this->params['status'])];
        }

        if (isset($this->params['page_type']) and $this->params['page_type']) {
            $page_start = intval($this->params['page_start'] ?? 1);
            $page_end   = intval($this->params['page_end']   ?? 1);
            $this->limitLength = $page_end * 15;
            $this->limitOffset = ($page_start - 1) * 15;
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
        $model = new KeyRule();
        $lists = $model
            ->field('id,model_id,type,channel,rule,prompt,status,create_time,update_time')
            ->where($this->setSearch())
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select()
            ->toArray();

        $modelIds = [];
        foreach ($lists as $item) {
            if (in_array($item['type'], [ChatEnum::MODEL_TYPE_CHAT, ChatEnum::MODEL_TYPE_EMB])) {
                $modelIds[] = intval($item['model_id']);
            }
        }

        $modelArray = [];
        if ($modelIds) {
            $modelArray = (new Models())->whereIn('id', array_unique($modelIds))->column('name', 'id');
        }

        foreach ($lists as &$item) {
            if (in_array($item['type'], [ChatEnum::MODEL_TYPE_CHAT, ChatEnum::MODEL_TYPE_EMB])) {
                $item['channel'] = $modelArray[$item['model_id']]??'-';
            } else {
                $item['channel'] = VoiceEnum::getChannel($item['channel']);
            }
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
        $model = new KeyRule();
        return $model
            ->where($this->setSearch())
            ->count();
    }

    /**
     * @notes 导出字段
     * @return array
     * @author fzr
     */
    public function setExcelFields(): array
    {
        return [
            'channel'     => '接口类型',
            'rule'        => '停用规则',
            'prompt'      => '通用提示',
            'status'      => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间'
        ]??[];
    }

    /**
     * @notes 导出名称
     * @return string
     * @author fzr
     */
    public function setFileName(): string
    {
        return 'key下架规则列表';
    }
}