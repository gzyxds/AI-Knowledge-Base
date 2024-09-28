<?php

namespace app\common\model\chat;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Models extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    public function modelsLists(){

        return $this->hasMany(ModelsCost::class,'model_id','id');

    }
}