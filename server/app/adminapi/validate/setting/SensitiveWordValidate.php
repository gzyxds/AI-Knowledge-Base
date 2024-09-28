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
 * 敏感词过滤参数
 */
class SensitiveWordValidate extends BaseValidate
{
    protected $rule = [
        'id'           => 'require',
        'word'         => 'require|array',
        'status'       => 'require|in:0,1',
        'is_sensitive' => 'require|in:0,1',
    ];

    protected $message = [
        'id.require'            => '请选择敏感词',
        'word.require'          => '请输入敏感词',
        'word.array'            => '敏感词值错误',
        'status.require'        => '请选择状态',
        'status.in'             => '状态值错误',
        'is_sensitive.require'  => '请选择是否开启敏感词库',
        'is_sensitive.in'       => '敏感词库值错误'
    ];

    public function sceneAdd(): SensitiveWordValidate
    {
        return $this->only(['word','status']);
    }

    public function sceneId(): SensitiveWordValidate
    {
        return $this->only(['id']);
    }

    public function sceneEdit(): SensitiveWordValidate
    {
        return $this->only(['id','word','status']);
    }

    public function sceneSetConfig(): SensitiveWordValidate
    {
        return $this->only(['is_sensitive']);
    }
}