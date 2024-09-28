<?php
namespace app\api\controller;
use app\api\logic\SquareCategoryLogic;

class SquareCategoryController extends BaseApiController
{


    public array $notNeedLogin = ['lists'];


    /**
     * @notes 广场列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2024/8/15 13:54
     */
    public function lists()
    {
        $type = $this->request->get('type',1);
        $share = $this->request->get('share','');
        $lists = (new SquareCategoryLogic())->lists($type,$share,$this->userId);
        return $this->success('',$lists);
    }

}