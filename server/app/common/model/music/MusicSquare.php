<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\common\model\music;


use app\common\enum\DrawSquareEnum;
use app\common\enum\MusicSquareEnum;
use app\common\model\BaseModel;
use app\common\model\draw\DrawRecords;
use app\common\model\draw\DrawSquareCategory;
use app\common\model\square\SquareCategory;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\storage\Driver as StorageDriver;
use think\facade\Log;
use think\model\concern\SoftDelete;

class MusicSquare extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    /**
     * @notes 审核状态
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2023/8/1 6:04 下午
     */
    public function getVerifyStatusDescAttr($value,$data)
    {
        return MusicSquareEnum::getVerifyStatusDesc($data['verify_status']);
    }

    /**
     * @notes 用户信息
     * @param $value
     * @param $data
     * @return array
     * @author ljj
     * @date 2023/8/1 6:10 下午
     */
    public function getUserInfoAttr($value,$data)
    {
        $result = [];
        if ($data['source'] == MusicSquareEnum::SOURCE_ADMIN) {
            $image = empty($data['avatar']) ? ConfigService::get('website', 'pc_logo') : $data['avatar'];
            $name = empty($data['nickname']) ? ConfigService::get('website', 'pc_title') : $data['nickname'];
            $result = [
                'image' => FileService::getFileUrl($image),
                'name' => $name,
            ];
        }
        if ($data['source'] == MusicSquareEnum::SOURCE_USER) {
            $user = User::where('id',$data['operate_id'])->findOrEmpty()->toArray();
            $result = [
                'image' => $user['avatar'] ?? '',
                'name' => $user['nickname'] ?? '',
            ];
        }

        return $result;
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
     * @notes 分类名称
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2023/8/31 11:55 上午
     */
    public function getCategoryNameAttr($value,$data)
    {
        return SquareCategory::where('id',$data['category_id'])->value('name') ?? '-';
    }

    /**
     * @notes 来源
     * @param $value
     * @param $data
     * @return string|string[]
     * @author ljj
     * @date 2023/8/31 12:12 下午
     */
    public function getSourceDescAttr($value,$data)
    {
        return MusicSquareEnum::getSourceDesc($data['source']);
    }


    /**
     * @notes 头像
     * @param $value
     * @return string
     * @author ljj
     * @date 2023/12/5 10:41 上午
     */
    public function getAvatarAttr($value)
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * @notes 头像
     * @param $value
     * @return mixed|string
     * @author ljj
     * @date 2023/12/5 10:41 上午
     */
    public function setAvatarAttr($value)
    {
        return trim($value) ? FileService::setFileUrl($value) : '';
    }
    /**
     * @notes 图片地址
     * @param $value
     * @return string
     * @author mjf
     * @date 2024/5/30 14:44
     */
    public function setImageUrlAttr($value): string
    {
        return FileService::setFileUrl($value);
    }

    /**
     * @notes 图片地址
     * @param $value
     * @return string
     * @author mjf
     * @date 2024/5/30 14:44
     */
    public function setImageLargeUrlAttr($value): string
    {
        return FileService::setFileUrl($value);
    }

    /**
     * @notes 视频地址
     * @param $value
     * @return string
     * @author mjf
     * @date 2024/5/30 14:44
     */
    public function setVideoUrlAttr($value): string
    {
        return FileService::setFileUrl($value);
    }

    /**
     * @notes 音频地址
     * @param $value
     * @return string
     * @author mjf
     * @date 2024/5/30 14:44
     */
    public function setAudioUrlAttr($value): string
    {
        return FileService::setFileUrl($value);
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


}