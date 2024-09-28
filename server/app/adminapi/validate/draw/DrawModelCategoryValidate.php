<?php

namespace app\adminapi\validate\draw;

use app\common\model\draw\DrawModel;
use app\common\model\draw\DrawModelCategory;
use app\common\validate\BaseValidate;

class DrawModelCategoryValidate extends BaseValidate
{
    protected $rule = [
        'id'     => 'require',
        'name'   => 'require|unique:' . DrawModelCategory::class . ',name',
        'status' => 'require',
    ];

    protected $message = [
        'id.require'     => '分类不存在',
        'name.require'   => '请填写分类名称',
        'name.unique'    => '该分类已存在',
        'status.require' => '请选择状态',
    ];

    protected $field = [
        'id'     => 'id',
        'name'   => '分类名称',
        'status' => '状态',
    ];


    public function sceneAdd(): DrawModelCategoryValidate
    {
        return $this->only(['name', 'status']);
    }

    public function sceneEdit(): DrawModelCategoryValidate
    {
        return $this->remove([
            'name' => 'unique'
        ]);
    }

    public function sceneDelete(): DrawModelCategoryValidate
    {
        return $this->only(['id'])->append("id", "checkDelete");
    }

    public function sceneId(): DrawModelCategoryValidate
    {
        return $this->only(['id']);
    }

    /**
     * @notes 删除校验
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author JXDN
     * @date 2024/5/15 11:18
     */
    public function checkDelete($value, $rule, $data): bool|string
    {
        $prompt = DrawModel::where('category_id', $value)->findOrEmpty();
        if (!$prompt->isEmpty()) {
            return "关键词分类已使用， 需移除分类关联关键词后再作删除";
        }

        return true;
    }
}