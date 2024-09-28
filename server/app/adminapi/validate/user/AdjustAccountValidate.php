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

namespace app\adminapi\validate\user;

use app\common\enum\user\AccountLogEnum;
use app\common\validate\BaseValidate;

/**
 * 调整账户额度参数验证器
 */
class AdjustAccountValidate extends BaseValidate
{
    protected $rule = [
        'scene'   => 'require|in:balance,robot,video',
        'action'  => 'require|in:' . AccountLogEnum::INC . ',' .AccountLogEnum::DEC,
        'user_id' => 'require|number',
        'num'     => 'require|float|gt:0',
    ];

    protected $message = [
        'id.require'     => '请选择用户',
        'action.require' => '请选择调整类型',
        'action.in'      => '调整类型错误',
        'num.require'    => '请输入调整数量',
        'num.float'      => '调整数量必须为数字',
        'num.gt'         => '调整数量必须大于零'
    ];
}