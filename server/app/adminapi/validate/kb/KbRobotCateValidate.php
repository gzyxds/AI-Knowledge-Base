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

namespace app\adminapi\validate\kb;

use app\common\validate\BaseValidate;

/**
 * 机器人分类参数验证器
 */
class KbRobotCateValidate extends BaseValidate
{
    protected $rule = [
        'id'       => 'require|number',
        'name'      => 'require|min:2|max:30',
        'image'     => 'max:250',
        'sort'      => 'number',
        'is_enable' => 'require|in:0,1',
    ];

    protected $message = [
        'id.require'        => 'id参数缺失',
        'id.number'         => 'id参数必须为数字',
        'name.require'      => '请填写分类名称',
        'name.min'          => '分类名称不能小于2个字符',
        'name.max'          => '分类名称不能大于30个字符',
        'image.max'         => '图标的路径超出长度',
        'sort.number'       => '排序编号必须为数字',
        'is_enable.require' => '请选择分类状态',
        'is_enable.in'      => '分类状态选择异常: [0, 1]',

    ];

    public function sceneId(): KbRobotCateValidate
    {
        return $this->only(['id']);
    }

    public function sceneAdd(): KbRobotCateValidate
    {
        return $this->remove('id', 'require');
    }
}