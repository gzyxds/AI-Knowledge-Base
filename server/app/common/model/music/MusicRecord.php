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

namespace app\common\model\music;

use app\common\enum\MusicEnum;
use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

/**
 * 音乐记录模型
 */
class MusicRecord extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    /**
     * @notes 状态
     * @param $value
     * @return array|string
     * @author mjf
     * @date 2024/5/30 15:19
     */
    public function getStatusDescAttr($value, $data): array|string
    {
        return MusicEnum::getStatusDesc($data['status']);
    }

    /**
     * @notes 时间
     * @param $value
     * @param $data
     * @return string
     * @author mjf
     * @date 2024/5/30 10:08
     */
    public function getDurationAttr($value, $data): string
    {
        $minutes = floor($value / 60);
        $remainingSeconds = $value % 60;

        // 格式化分钟和秒数为两位数字
        $formattedMinutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);
        $formattedSeconds = str_pad($remainingSeconds, 2, "0", STR_PAD_LEFT);

        // 返回格式化的时间字符串
        return "$formattedMinutes:$formattedSeconds";
    }

    /**
     * @notes 图片地址
     * @param $value
     * @return string
     * @author mjf
     * @date 2024/5/30 14:44
     */
    public function getImageUrlAttr($value): string
    {
        return FileService::getFileUrl($value);
    }

    /**
     * @notes 图片地址
     * @param $value
     * @return string
     * @author mjf
     * @date 2024/5/30 14:44
     */
    public function getImageLargeUrlAttr($value): string
    {
        return FileService::getFileUrl($value);
    }

    /**
     * @notes 视频地址
     * @param $value
     * @return string
     * @author mjf
     * @date 2024/5/30 14:44
     */
    public function getVideoUrlAttr($value): string
    {
        return FileService::getFileUrl($value);
    }

    /**
     * @notes 音频地址
     * @param $value
     * @return string
     * @author mjf
     * @date 2024/5/30 14:44
     */
    public function getAudioUrlAttr($value): string
    {
        return FileService::getFileUrl($value);
    }

    /**
     * @notes 风格
     * @param $value
     * @param $data
     * @return string
     * @author mjf
     * @date 2024/5/30 16:44
     */
    public function getStyleDescAttr($value, $data): string
    {
        if (!empty($data['style_id'])) {
            $data['style_id'] = explode(',', $data['style_id']);
            $styleLists = MusicStyle::whereIn('id', $data['style_id'])->select()->toArray();
            $styleArr = [];
            foreach ($styleLists as $style) {
                $styleArr[] = !empty($style['value']) ? $style['value'] : $style['name'];
            }
            return implode(',', $styleArr);
        } else {
            return $data['tags'];
        }
    }



}