<?php

namespace app\adminapi\logic\draw;

use app\common\logic\BaseLogic;
use app\common\model\draw\DrawModel;
use app\common\model\draw\DrawModelCategory;

class DrawModelCategoryLogic extends BaseLogic
{
    /**
     * @notes 列表
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/14 17:01
     */
    public static function lists($params): array
    {
        $where = [];
        if (!empty($params['name'])) {
            $where[] = ['name', 'like', '%' . $params['name'] . '%'];
        }
        if (isset($params['status']) && $params['status'] != '') {
            $where[] = ['status', '=', $params['status']];
        }

        // 使用 withCount 计算每个分类下的 model 数量
        return DrawModelCategory::withCount(['models' => 'model_count'])
            ->where($where)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }




    /**
     * @notes
     * @param array $params
     * @return DrawModelCategory|\think\Model
     * @author 段誉
     * @date 2023/6/14 16:20
     */
    public static function add(array $params)
    {
        return DrawModelCategory::create([
            'name' => $params['name'],
            'sort' => $params['sort'] ?? 0,
            'status' => $params['status'] ?? 1
        ]);
    }

    /**
     * @notes 编辑
     * @param array $params
     * @return DrawModelCategory
     * @author 段誉
     * @date 2023/6/14 16:20
     */
    public static function edit(array $params)
    {
        return DrawModelCategory::where('id', $params['id'])->update([
            'name' => $params['name'],
            'sort' => $params['sort'] ?? 0,
            'status' => $params['status']
        ]);
    }

    /**
     * @notes 删除
     * @param array $params
     * @return bool
     * @throws \Exception
     * @author 段誉
     * @date 2023/6/14 16:21
     */
    public static function delete(array $params): bool
    {
        $existing = (new DrawModel())->where(['category_id' => $params["id"]])->findOrEmpty();
        if (!$existing->isEmpty()) {
            throw new \Exception('该分类已经被使用,不能删除');
        }
        return DrawModelCategory::destroy($params['id']);
    }

    /**
     * @notes 详情
     * @param $params
     * @return array
     * @author 段誉
     * @date 2023/6/14 16:21
     */
    public static function detail($params): array
    {
        return DrawModelCategory::findOrEmpty($params['id'])->toArray();
    }

    /**
     * @notes 状态切换
     * @param int $id
     * @return bool
     * @author 段誉
     * @date 2023/6/15 10:56
     */
    public static function status(int $id)
    {
        $questionCategory = DrawModelCategory::where(['id' => $id])->findOrEmpty();
        if ($questionCategory->isEmpty()) {
            return true;
        }
        $questionCategory->status = $questionCategory->status ? 0 : 1;
        $questionCategory->save();
        return true;
    }


    /**
     * @notes 全部列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 10:15
     */
    public static function all()
    {
        return DrawModelCategory::where(['status' => 1])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }
}