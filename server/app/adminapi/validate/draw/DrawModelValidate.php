<?php

namespace app\adminapi\validate\draw;

use app\common\model\draw\DrawModel;
use app\common\validate\BaseValidate;

class DrawModelValidate extends BaseValidate
{
    protected $rule = [
        'id'          => 'require',
        'model_name'  => 'require|unique:' . DrawModel::class . ',model_name',
        'cover'       => 'require',
        'category_id' => 'require',
        'sort'        => 'require|number',
        'status'      => 'require',
    ];

    protected $message = [
        'id.require'          => '模型不存在',
        'model_name.require'  => '请填写模型标识',
        'model_name.unique'   => '模型已存在',
        'category_id.require' => '请选择模型分类',
        'sort.require'        => '请输入排序',
        'sort.number'         => '排序值错误',
        'status.require'      => '请选择状态',
    ];

    protected function sceneAdd(): DrawModelValidate
    {
        return $this->remove('id', true);
    }

    protected function sceneEdit(): DrawModelValidate
    {
        return $this->remove([
            'model_name' => 'unique'
        ]);
    }

    protected function sceneId(): DrawModelValidate
    {
        return $this->only(['id']);
    }
}