<?php
namespace app\api\logic;
use app\common\model\square\SquareCategory;

/**
 * 分类列表
 * Class SquareCategoryLogic
 * @package app\api\logic
 */
class SquareCategoryLogic
{


    /**
     * @notes 分类列表
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2024/8/7 10:20
     */
    public function lists($type,$share,$userId)
    {
        $lists = SquareCategory::where(['type'=>$type])
            ->field('id,name')
            ->select()
            ->toArray();
        if(!$share && $userId){
            array_unshift($lists,['id'=>0,'name'=>'收藏']);
        }

        return $lists;

    }

}