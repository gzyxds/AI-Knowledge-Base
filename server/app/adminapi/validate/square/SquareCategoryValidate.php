<?php
namespace app\adminapi\validate\square;
use app\common\validate\BaseValidate;

/**
 * 广场分类验证器类
 * Class SquareCategoryValidate
 * @package app\adminapi\validate\square
 */
class  SquareCategoryValidate extends BaseValidate {
    protected $rule = [
        'name'      => 'require|max:32',
        'type'      => 'require|in:1,2,3',
        'status'    => 'require|in:0,1',
    ];

    protected $message = [
        'name.require'  => '请输入名称',
        'name.max'      => '名称不可成功32个字符',
        'type.require'  => '请选择应用',
        'type.in'       => '请选择应用',
        'status.require'=> '请选择状态',
        'status.in'     => '状态错误',
    ];

    protected function sceneEdit(){
        return $this->remove(['id'=>true]);
    }

    protected function sceneId()
    {
        return $this->only(['id']);
    }

}