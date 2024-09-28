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

use app\common\enum\VideoEnum;
use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

/**
 * 音乐记录模型
 */
class VideoRecord extends BaseModel
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
        return VideoEnum::getStatusDesc($data['status']);
    }

    /**
     * @notes 生成类型
     * @param $value
     * @return array|string
     * @author mjf
     * @date 2024/5/30 15:19
     */
    public function getTypeDescAttr($value, $data): array|string
    {
        return VideoEnum::getTypeDesc($data['type']);
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
            $styleLists = VideoStyle::whereIn('id', $data['style_id'])->select()->toArray();
            $styleArr = [];
            foreach ($styleLists as $style) {
                $styleArr[] = !empty($style['value']) ? $style['value'] : $style['name'];
            }
            return implode(',', $styleArr);
        } else {
            return $data['tags'] ?? '';
        }
    }

}