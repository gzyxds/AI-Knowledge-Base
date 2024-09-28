<?php

namespace app\adminapi\lists\draw;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\draw\DrawLora;
use app\common\service\FileService;

class DrawLoraLists extends BaseAdminDataLists implements ListsSearchInterface
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
        $model = new DrawLora();

        return $model->withoutField('update_time,delete_time')
            ->where($this->searchWhere)
            ->order(['sort' => 'desc', 'id' => 'asc'])
            ->withAttr(['cover' => function ($value) {
                return FileService::getFileUrl($value);
            }])
            ->select()
            ->toArray();
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public function count(): int
    {
        return (new DrawLora())->where($this->searchWhere)->count();
    }

    /**
     * @notes 条件
     * @return array
     * @author fzr
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['title'],
            '=' => ['status'],
        ] ?? [];
    }
}