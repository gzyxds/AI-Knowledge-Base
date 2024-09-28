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

namespace app\adminapi\validate\write;

use app\common\validate\BaseValidate;

/**
 * 写作问题参数验证器
 */
class WriteProblemValidate extends BaseValidate
{
    protected $rule = [
        'id'     => 'require|number',
        'q'      => 'require',
        'a'      => 'require',
        'sort'   => 'number',
        'status' => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'    => 'id不能为空',
        'q.require'     => '问题不能为空',
        'a.require'     => '答案不能为空',
        'sort.number'   => '排序编号必须为数字',
        'status.number' => '请设置状态',
        'status.in'     => '状态选择异常'
    ];

    public function sceneId(): WriteProblemValidate
    {
        return $this->only(['id']);
    }

    public function sceneAdd(): WriteProblemValidate
    {
        return $this->remove('id', 'require');
    }
}