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

use app\common\model\user\User;
use app\common\validate\BaseValidate;

/**
 * 用户验证
 */
class UserValidate extends BaseValidate
{
    protected $regex = [
        'password' => '/^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)(?!([^(0-9a-zA-Z)]|[\(\)])+$)([^(0-9a-zA-Z)]|[\(\)]|[a-z]|[A-Z]|[0-9]){6,20}$/'
    ];
    protected $rule = [
        'id'    => 'require|checkUser',
        'field' => 'require|checkField',
        'value' => 'require',
        // 'member_perpetual' => 'require|in:0,1',

        'avatar'   => 'require|max:200',
        'nickname' => 'require|max:32',
        'mobile'   => 'require|mobile',
        'email' => 'requireWithout:mobile|email',
        'password' => 'require|min:6|max:30|regex:password',

        'action' => 'require|in:1,2',
        'number' => 'require|number'
    ];

    protected $message = [
        'id.require'    => '请选择用户',
        'field.require' => '请选择操作',
        'value.require' => '请输入内容',

        'avatar.require'   => '请上传头像',
        'avatar.max'       => '用户头像异常',
        'nickname.require' => '请填写用户昵称',
        'nickname.max'     => '用户昵称不能超出32个字符',
        'mobile.require'   => '请填写手机号码',
        'mobile.mobile'    => '手机号码格式不正确',
        'password.require' => '请填写登录密码',
        'password.min'     => '登录密码最少6位数',
        'password.max'     => '登录密码最少30位数',
        'password.regex'   => '登录密码须为数字,字母或符号组合',

        'action.require'   => '请选择调整方式',
        'action.in'        => '选择的调整方式异常',
        'number.require'   => '请填写调整数量',
        'number.number'    => '调整数量只能数数字',
    ];

    public function sceneAdjustMember(): UserValidate
    {
        return $this->only(['id','member_perpetual']);
    }

    public function sceneAdjustSpace(): UserValidate
    {
        return $this->only(['id','action', 'number']);
    }

    /**
     * @notes 详情场景
     * @return UserValidate
     * @author 段誉
     * @date 2022/9/22 16:35
     */
    public function sceneDetail(): UserValidate
    {
        return $this->only(['id']);
    }

    /**
     * @notes 创建场景
     * @return UserValidate
     * @author fzr
     */
    public function sceneCreate(): UserValidate
    {
        return $this->only(['avatar', 'nickname', 'mobile', 'password']);
    }

    public function sceneAdjustLeader(): UserValidate
    {
        return $this->only(['id','adjust_type','leader_id']);
    }

    /**
     * @notes 重置密码场景
     * @return UserValidate
     * @author fzr
     */
    public function sceneResetPwd(): UserValidate
    {
        return $this->only(['id', 'password']);
    }

    /**
     * @notes 信息编辑场景
     * @return UserValidate
     * @author fzr
     */
    public function sceneSetInfo(): UserValidate
    {
        return $this->only(['id', 'field', 'value']);
    }

    /**
     * @notes 用户信息校验
     * @param $value
     * @return bool|string
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/22 17:03
     */
    public function checkUser($value): bool|string
    {
        $userIds = is_array($value) ? $value : [$value];

        foreach ($userIds as $item) {
            if (!(new User())->find($item)) {
                return '用户不存在！';
            }
        }
        return true;
    }

    /**
     * @notes 校验是否可更新信息
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author 段誉
     * @date 2022/9/22 16:37
     */
    public function checkField($value, $rule, $data): bool|string
    {
        unset($rule);
        $allowField = ['account', 'sex', 'mobile', 'real_name', 'email', 'multipoint_login'];

        if (!in_array($value, $allowField)) {
            return '用户信息不允许更新';
        }

        switch ($value) {
            case 'account':
                //验证手机号码是否存在
                $account = (new User())->where([
                    ['id', '<>', $data['id']],
                    ['account', '=', $data['value']]
                ])->findOrEmpty();

                if (!$account->isEmpty()) {
                    return '账号已被使用';
                }
                break;

            case 'mobile':
                if (!$this->validate($data['value'], 'mobile', $data)) {
                    return '手机号码格式错误';
                }

                //验证手机号码是否存在
                $mobile = (new User())->where([
                    ['id', '<>', $data['id']],
                    ['mobile', '=', $data['value']]
                ])->findOrEmpty();

                if (!$mobile->isEmpty()) {
                    return '手机号码已存在';
                }
                break;
            case 'email':
                if (!$this->validate($data['value'], 'email', $data)) {
                    return '邮箱格式错误';
                }

                //验证邮箱是否存在
                $email = User::where([
                    ['id', '<>', $data['id']],
                    ['email', '=', $data['value']]
                ])->findOrEmpty();

                if (!$email->isEmpty()) {
                    return '邮箱已存在';
                }
                break;
        }
        return true;
    }
}