<?php

namespace app\adminapi\validate\draw;

use app\common\model\draw\DrawLora;
use app\common\validate\BaseValidate;

class DrawLoraValidate extends BaseValidate
{
    protected $rule = [
        'id'     => 'require',
        'model_name'   => 'require|unique:'.DrawLora::class.',model_name',
        'cover' => 'require',
        'sort'   => 'require|number',
        'status' => 'require',
    ];

    protected $message = [
        'id.require'     => '请选择分类',
        'model_name.require'   => '请选择模型文件',
        'model_name.unique'    => '模型文件已存在',
        'sort.require'   => '请输入排序',
        'sort.number'    => '排序值错误',
        'status.require' => '请选择状态',
    ];

    protected function sceneAdd(): DrawLoraValidate
    {
        return $this->remove('id',true);
    }

    protected function sceneId(): DrawLoraValidate
    {
        return $this->only(['id']);
    }
}