<?php

namespace app\common\model\draw;

use app\common\model\BaseModel;

class DrawModelCategory extends BaseModel
{
// 定义与Model模型的关联关系
    public function models(): \think\model\relation\HasMany
    {
        return $this->hasMany(DrawModel::class, 'category_id', 'id');
    }
}