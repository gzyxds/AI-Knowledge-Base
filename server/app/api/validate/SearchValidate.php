<?php

namespace app\api\validate;

use app\common\validate\BaseValidate;

/**
 * Ai搜索参数验证器
 */
class SearchValidate extends BaseValidate
{
    protected $rule = [
        'model'  => 'require|in:search,copilot,research',
        'type'   => 'require|in:all,doc,scholar',
        'ask'    => 'require|max:800',
        'probe'  => 'in:0,1'
    ];

    protected $message = [
        'model.require' => '请选择搜索模式',
        'model.in'      => '不支持的搜索模式',
        'type.require'  => '请选择搜索类型',
        'type.in'       => '不支持的搜索类型',
        'ask.require'   => '请输入要搜索的问题',
        'ask.max'       => '搜索的问题不能超出800个字符',
        'probe.in'      => '追问数据异常'
    ];
}