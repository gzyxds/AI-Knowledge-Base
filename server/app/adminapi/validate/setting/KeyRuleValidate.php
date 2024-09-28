<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\adminapi\validate\setting;

use app\common\validate\BaseValidate;

/**
 * Key规则参数验证器
 */
class KeyRuleValidate extends BaseValidate
{
    protected $rule = [
        'id'        => 'require',
        'model_id'  => 'number',
        'type'      => 'require|in:1,2,3,4,5',
        'channel'   => 'require',
        'rule'      => 'require',
        'prompt'    => 'require',
        'status'    => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'      => '参数缺失',
        'type.require'    => '规则类型缺失',
        'type.in'         => '规则类型错误',
        'channel.require' => '请选择接口类型',
        'rule.require'    => '请输入停用规则',
        'prompt.require'  => '请输入停用提示',
        'status.require'  => '请选择状态',
        'status.in'       => '状态错误'
    ];

    public function sceneId(): KeyRuleValidate
    {
        return $this->only(['id']);
    }

    public function sceneAdd(): KeyRuleValidate
    {
        return $this->remove('id',true);
    }
}