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

namespace app\adminapi\validate\recharge;

use app\common\validate\BaseValidate;

class RechargePackageValidate extends BaseValidate
{
    protected $rule = [
        'id'                  => 'require|number',
        'name'                => 'require|max:250',
        'remarks'             => 'max:800',
        'sell_price'          => 'require|float|checkNumber',
        'line_price'          => 'float',
        'chat_balance'        => 'number|max:10',
        'robot_number'        => 'number|max:10',
        'video_duration'      => 'number|max:10',
        'give_chat_balance'   => 'number|max:10',
        'give_robot_number'   => 'number|max:10',
        'give_video_duration' => 'number|max:10',
        'status'              => 'require|in:0,1',
        'is_give'             => 'require|in:0,1'
    ];

    protected $message = [
        'id.require'                 => 'ID缺失',
        'id.number'                  => 'ID必须为数字',
        'name.require'               => '请输入套餐名称',
        'name.max'                   => '套餐名称不能超出250个字符',
        'remarks.max'                => '套餐描述不能超出800个字符',
        'sell_price.require'         => '请输入销售价格',
        'sell_price.float'           => '销售价格必须为浮点数',
        'line_price.float'           => '划线必须为浮点数',
        'chat_balance.number'        => '电力值数量输入格式异常',
        'chat_balance.max'           => '电力值数量不能超过10位数',
        'robot_number.number'        => '机器人数量必须为数字',
        'robot_number.max'           => '机器人数量不能超过10位数',
        'video_duration.number'      => '视频合成时长必须为数字',
        'video_duration.max'         => '视频合成时长不能超过10位数',
        'give_chat_balance.number'   => '赠送问答的数量必须为数字',
        'give_chat_balance.max'      => '赠送电力值的数量不能超过10位数',
        'give_robot_number.max'      => '赠送机器人数量不能超过10位数',
        'give_video_duration.number' => '赠送视频合成时长必须为数字',
        'give_video_duration.max'    => '赠送视频合成市场不能超过10位数',
        'status.require'             => '请选择套餐状态',
        'status.in'                  => '套餐状态选择异常',
        'is_give.require'            => '请选择赠送状态',
        'is_give.in'                 => '赠送状态选择异常'
    ];

    public function sceneAdd(): RechargePackageValidate
    {
        return $this->remove('id', true);
    }

    public function sceneId(): RechargePackageValidate
    {
        return $this->only(['id']);
    }

    public function checkNumber($value, $rules, $data): bool|string
    {
        unset($value);
        unset($rules);
        if (!($data['chat_balance']??0) && !($data['robot_number']??0)) {
            return '套餐内容至少有一个不能为0';
        }

        if (!empty($data['chat_balance']) && $data['chat_balance'] < 0) {
            return '问答的数量不能少于0';
        }

        if (!empty($data['robot_number']) && $data['robot_number'] < 0) {
            return '机器人的数量不能少于0';
        }

        if (!empty($data['video_duration']) && $data['video_duration'] < 0) {
            return '视频合成时长不能少于0';
        }

        // 赠送检测
        if (!empty($data['give_chat_balance']) && $data['give_chat_balance'] < 0) {
            return '赠送的问答数量不能少于0';
        }

        if (!empty($data['give_robot_number']) && $data['give_robot_number'] < 0) {
            return '赠送的机器人数量不能少于0';
        }

        if (!empty($data['give_video_duration']) && $data['give_video_duration'] < 0) {
            return '赠送的视频合成时长不能少于0';
        }

        return true;
    }
}