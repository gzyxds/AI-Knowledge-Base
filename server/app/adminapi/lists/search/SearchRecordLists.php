<?php

namespace app\adminapi\lists\search;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\SearchEnum;
use app\common\model\search\AiSearchRecord;
use app\common\service\FileService;

/**
 * Ai搜索记录列表
 */
class SearchRecordLists extends BaseAdminDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author fzr
     */
    public function where(): array
    {
        $where = [];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname', 'like', '%'.$this->params['user_info'].'%'];
        }

        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['s.ask', 'like', '%'.$this->params['keyword'].'%'];
        }

        if (!empty($this->params['start_time']) && $this->params['start_time']) {
            $where[] = ['s.create_time', '>=', strtotime($this->params['start_time'])];
        }

        if (!empty($this->params['end_time']) && $this->params['end_time']) {
            $where[] = ['s.create_time','<=', strtotime($this->params['end_time'])];
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
        $lists = (new AiSearchRecord())
            ->alias('s')
            ->field(['s.id,s.user_id,u.nickname,u.avatar,s.model,s.price,s.type,s.ask,s.markdown,s.ip,s.create_time'])
            ->join('user u', 'u.id = s.user_id')
            ->order('id desc')
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['avatar'] = FileService::getFileUrl($item['avatar']);
            $item['model'] = SearchEnum::getModelDesc($item['model']);
            $item['type'] = SearchEnum::getTypeDesc($item['type']);
            $item['price'] = format_amount_zero($item['price']);
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
        return (new AiSearchRecord())
            ->alias('s')
            ->field(['s.id,s.user_id,u.nickname,u.avatar,s.model,s.type,s.ask,s.create_time'])
            ->join('user u', 'u.id = s.user_id')
            ->where($this->where())
            ->count();
    }
}