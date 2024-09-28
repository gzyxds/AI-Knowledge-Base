<?php

namespace app\adminapi\validate\setting;

use app\common\validate\BaseValidate;

class SpaceValidate extends BaseValidate
{
    protected $rule = [
        'space' => 'require|number'
    ];

    protected $message = [
        'space.require' => '请填写空间大小',
        'space.number ' => '空间大小的值只能是数字'
    ];
}