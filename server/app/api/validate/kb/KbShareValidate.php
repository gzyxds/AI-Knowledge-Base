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

namespace app\api\validate\kb;

use app\common\validate\BaseValidate;

/**
 * 机器人发布参数验证器
 */
class KbShareValidate extends BaseValidate
{
    protected $rule = [
        'id'       => 'require|number',
        'type'     => 'require|number',
        'robot_id' => 'require|number',
        'name'     => 'require',
        'password' => 'min:6|max:30',

        'limit_total_chat' => 'require|number',
        'limit_today_chat' => 'require|number',
        'limit_exceed'     => 'max:500',
        'url'              => 'require'
    ];

    protected $message = [
        'id.require'       => 'ID参数缺失',
        'type.require'     => '请选择发布类型',
        'robot_id.require' => '请选择机器人',
        'name.require'     => '请填写发布名称',
        'password.min'     => '密码不能少于6个字符',
        'password.max'     => '密码不能大于30个字符',

        'limit_total_chat.require' => '请填写用户总对话数',
        'limit_today_chat.require' => '请填写用户每天总对话数',
        'limit_exceed.max'         => '超出默认回复的字符不能超出500个字符',
    ];

    protected $scene = [
        'lists' => ['type', 'robot_id'],
        'add'   => ['type', 'robot_id', 'name', 'password'],
        'edit'  => ['id', 'limit_total_chat', 'limit_today_chat', 'limit_exceed'],
        'del'   => ['id', 'type'],
        'setBg' => ['id', 'url'],
        'update' => ['id', 'name', 'password']
    ];
}