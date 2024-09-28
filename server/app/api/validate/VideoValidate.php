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

use app\common\enum\VideoEnum;
use app\common\validate\BaseValidate;

/**
 * 视频参数验证
 */
class VideoValidate extends BaseValidate
{
    protected $rule = [
        'type'      => 'require',
        'prompt'    => 'require|max:1000',
        'scale'     => 'require',
        'image'     => 'requireIf:type,' . VideoEnum::IMAGE_TO_VIDEO,
    ];

    protected $message = [
        'type.require'      => '生成类型参数缺失',
        'prompt.require'    => '请填写描述词',
        'prompt.max'        => '描述词不能超过1000个字符',
        'scale.require'     => '请选择生成尺寸',
        'image.requireIf'   => '请上传参考图',
    ];

    public function sceneGenerate(): VideoValidate
    {
        return $this->only(['type', 'prompt', 'scale', 'image']);
    }
}