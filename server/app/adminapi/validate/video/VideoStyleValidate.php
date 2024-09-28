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

namespace app\adminapi\validate\video;

use app\common\model\video\VideoStyle;
use app\common\validate\BaseValidate;

/**
 * 音乐风格验证器类
 */
class VideoStyleValidate extends BaseValidate
{
    protected $rule = [
        'id'     => 'require',
        'image'  => 'require',
        'name'   => 'require|max:64|unique:' . VideoStyle::class . ',name',
        'sort'   => 'require|number',
        'status' => 'require'
    ];

    protected $message = [
        'id.require'     => '请选择风格',
        'image.require'  => '请选择风格封面',
        'name.require'   => '请输入名称',
        'name.max'       => '名称不能超过64个字符',
        'name.unique'    => '名称重复',
        'sort.require'   => '请输入排序',
        'sort.number'    => '排序值错误',
        'status.require' => '请选择状态'
    ];

    protected function sceneAdd(): VideoStyleValidate
    {
        return $this->remove('id',true);
    }

    protected function sceneId(): VideoStyleValidate
    {
        return $this->only(['id']);
    }
}