<?php

namespace app\adminapi\lists\draw;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\draw\DrawModel;
use app\common\model\draw\DrawModelCategory;
use app\common\model\draw\DrawModelLoraRelation;
use app\common\service\FileService;

class DrawModelLists extends BaseAdminDataLists implements ListsSearchInterface
{
    /**
     * @notes 列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author JXDN
     * @date 2024/5/15 18:13
     */
    public function lists(): array
    {
        $model = new DrawModel();
        $lists = $model->withoutField('update_time,delete_time')
            ->where($this->searchWhere)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->withAttr(['cover' => function ($value) {
                return FileService::getFileUrl($value);
            }])
            ->select()
            ->toArray();

        $category = new DrawModelCategory();

        $cateIds = array_column($lists, 'category_id');
        $cate = $category->whereIn('id', $cateIds)
            ->column('id,name', 'id');

        foreach ($lists as &$item) {
            $item['title'] = $item['title'] ?: $item['model_name'];
            $item['category_name'] = $cate[$item['category_id']]['name'] ?? '';
            $item['cover'] = FileService::getFileUrl($item['cover']);
        }

        $model_lora = new DrawModelLoraRelation();
        foreach ($lists as &$item) {
            $item['lora_total'] = $model_lora->where(['model_id' => $item['id']])->count();
        }

        return $lists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author JXDN
     * @date 2024/5/15 18:13
     */
    public function count(): int
    {
        return (new DrawModel())->where($this->searchWhere)->count();
    }

    /**
     * @notes 条件
     * @return array
     * @author JXDN
     * @date 2024/5/15 18:13
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['title'],
            '=' => ['status', 'category_id'],
        ] ?? [];
    }
}