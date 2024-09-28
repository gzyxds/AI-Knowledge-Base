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

namespace app\api\validate;

use app\common\validate\BaseValidate;

/**
 * 用户验证器
 */
class RechargeValidate extends BaseValidate
{
    protected $rule = [
        'package_id' => 'require|number',
        'pay_way'    => 'number',
    ];

    protected $message = [
        'package_id.require' => '请选择充值的套餐',
        'pay_way.number'     => '支付方式必须是数字',
    ];
}