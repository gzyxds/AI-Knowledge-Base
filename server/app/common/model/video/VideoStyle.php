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

namespace app\common\model\video;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 视频风格模型
 */
class VideoStyle extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    /**
     * @notes 状态
     * @param $value
     * @param $data
     * @return string
     * @author mjf
     * @date 2024/5/27 11:49
     */
    public function getStatusDescAttr($value, $data): string
    {
        return $data['status'] ? '开启' : '关闭';
    }

}