<?php

namespace app\common\model\draw;

use app\common\model\BaseModel;
use think\model\relation\BelongsToMany;

class DrawModel extends BaseModel
{
    public function loras(): BelongsToMany
    {
        return $this->belongsToMany(DrawLora::class, DrawModelLoraRelation::class, 'lora_id', 'model_id');
    }

    // 定义与ModelCategory模型的关联关系
    public function category(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo(DrawModelCategory::class, 'category_id', 'id');
    }
}