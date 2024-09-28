<?php
namespace app\adminapi\controller\square;
use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\square\SquareCategoryLogic;
use app\adminapi\validate\square\SquareCategoryValidate;

/**
 * 广场分类控制器类
 * Class SquareCategoryController
 * @package app\adminapi\controller\square
 */
class SquareCategoryController extends BaseAdminController
{


    /**
     * @notes 分类列表(不分页)
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/8/7 10:36
     */
    public function categoryLists()
    {
        $type = $this->request->get('type',1);
        $lists = (new SquareCategoryLogic())->categoryLists($type);
        return $this->success('',$lists);
    }

    /**
     * @notes 分类列表
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/8/7 10:35
     */
    public function lists()
    {

        return $this->dataLists();

    }


    /**
     * @notes 添加分类
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/8/6 18:07
     */
    public function add()
    {
        $params = (new SquareCategoryValidate())->post()->goCheck();
        (new SquareCategoryLogic())->add($params);
        return $this->success('添加成功', [], 1, 1);
    }


    /**
     * @notes 编辑分类
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/8/6 18:10
     */
    public function edit()
    {
        $params = (new SquareCategoryValidate())->post()->goCheck('edit');
        $result = (new SquareCategoryLogic())->edit($params);
        if(true === $result){
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail($result);
    }


    /**
     * @notes 修改状态
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/8/6 18:10
     */
    public function status()
    {
        $params = (new SquareCategoryValidate())->post()->goCheck('id');
        (new SquareCategoryLogic())->add($params['id']);
        return $this->success('修改成功', [], 1, 1);
    }


    /**
     * @notes 删除
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/8/6 18:10
     */
    public function del()
    {
        $params = (new SquareCategoryValidate())->post()->goCheck('id');
        $result = (new SquareCategoryLogic())->add($params['id']);
        if(true === $result){
            return $this->success('删除成功', [], 1, 1);
        }
        return $this->fail($result);
    }



}