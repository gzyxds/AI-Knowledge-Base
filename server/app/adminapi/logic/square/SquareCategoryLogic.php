<?php
namespace app\adminapi\logic\square;
use app\common\model\draw\DrawSquare;
use app\common\model\music\MusicSquare;
use app\common\model\square\SquareCategory;
use app\common\model\video\VideoSquare;

/**
 * 广场分类验证器类
 * Class SquareCategoryLogic
 * @package app\adminapi\logic\square
 */
class SquareCategoryLogic
{

    /**
     * @notes 分类列表
     * @param int $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2024/8/7 10:38
     */
    public function categoryLists(int $type){
        return SquareCategory::where(['type'=>$type])
            ->field('id,name')
            ->select()->toArray();
    }

    /**
     * @notes 添加分类
     * @param array $params
     * @return SquareCategory|\think\Model
     * @author cjhao
     * @date 2024/8/6 18:01
     */
    public function add(array $params)
    {
        return SquareCategory::create([
            'name'   => $params['name'],
            'type'   => $params['type'],
            'sort'   => $params['sort'] ?? 0,
            'status' => $params['status'],
        ]);

    }

    /**
     * @notes 编辑分类
     * @param array $params
     * @return string|true
     * @author cjhao
     * @date 2024/8/6 18:00
     */
    public function edit(array $params)
    {
        $squareCategory = SquareCategory::where(['id'=>$params['id']])->findOrEmpty()->toArray();
        if(empty($squareCategory)){
            return '分类不存在';
        }
        if($params['type'] != $squareCategory['type']){
            $square = [];
            switch ($squareCategory['type']){
                case 1:
                    $square = DrawSquare::where(['category_id'=>$params['id']])->findOrEmpty()->toArray();
                    break;
                case 2:
                    $square = MusicSquare::where(['category_id'=>$params['id']])->findOrEmpty()->toArray();
                    break;
                case 3:
                    $square = VideoSquare::where(['category_id'=>$params['id']])->findOrEmpty()->toArray();
                    break;
            }
            if($square){
                return '该分类已关联了用户分享数据，不允许修改所属类型';
            }
        }
        SquareCategory::where(['id'=>$params['id']])->update([
            'name'   => $params['name'],
            'sort'   => $params['sort'] ?? 0,
            'status' => $params['status'],
            'type'   => $params['type'],
        ]);
        return true;
    }

    /**
     * @notes 修改状态
     * @param int $id
     * @return true
     * @author cjhao
     * @date 2024/8/6 18:02
     */
    public function status(int $id)
    {
        $squareCategory = SquareCategory::where(['id'=>$id])->findOrEmpty();
        $squareCategory->status = $squareCategory->status ? 0 : 1;
        $squareCategory->save();
        return true;

    }

    /**
     * @notes
     * @param int $id
     * @return string|bool
     * @author cjhao
     * @date 2024/8/6 18:02
     */
    public function del(int $id)
    {
        $squareCategory = SquareCategory::where(['id'=>$id])->findOrEmpty();
        if($squareCategory->isEmpty()){
            return '分类不存在';
        }
        $square = [];
        switch ($squareCategory['type']){
            case 1:
                $square = DrawSquare::where(['id'=>$id])->findOrEmpty()->toArray();
                break;
            case 2:
                $square = MusicSquare::where(['id'=>$id])->findOrEmpty()->toArray();
                break;
            case 3:
                $square = VideoSquare::where(['id'=>$id])->findOrEmpty()->toArray();
                break;
        }
        if($square){
            return '该分类已关联了用户分享数据，不允许删除';
        }
        $squareCategory->delete();
        return true ;
    }

}