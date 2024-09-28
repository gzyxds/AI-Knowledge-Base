<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\common\enum\member;
use app\common\enum\draw\DrawEnum;

class MemberPackageEnum
{

    const APPLY_CHAT    = 1;
    const APPLY_VECTOR  = 2;

    const APPLY_DRAW    = 3;

    const APPLY_MUSIC   = 4;
    const APPLY_MINDMAP   = 5;

    const APPLY_VIDEO   = 6;

    const APPLY_AISEARCH = 7;

    //评价类型
    const TYPE_INVENTED = 1;//虚拟评价
    const TYPE_USER = 2;//用户自评


    //购买来源
    const CHANNEL_USER = 1;//用户自购
    const CHANNEL_ADMIN = 2;//后台调整
    const CHANNEL_CARDCODE = 3; //卡密兑换


    //套餐时长类型
    const DURATION_TYPE_DAY = 1;//日
    const DURATION_TYPE_MONTH = 2;//月

    const DURATION_PERPEUTAL = 3;//永久


    /**
     * @notes 评价类型
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2023/4/14 3:11 下午
     */
    public static function getTypeDesc($value = true)
    {
        $data = [
            self::TYPE_INVENTED => '虚拟评价',
            self::TYPE_USER => '用户自评'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }

    /**
     * @notes 购买来源
     * @param bool $value
     * @return string|string[]
     * @author ljj
     * @date 2023/6/20 8:19 下午
     */
    public static function getChannelDesc($value = true)
    {
        $data = [
            self::CHANNEL_USER => '用户自购',
            self::CHANNEL_ADMIN => '后台调整',
            self::CHANNEL_CARDCODE => '卡密兑换'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }


    /**
     * @notes 获取应用列表
     * @param $from
     * @return mixed|string|true
     * @author cjhao
     * @date 2024/6/3 10:18
     */
    public static function getApplyLissts($from = true)
    {
        $desc = [
            DrawEnum::API_SD        => DrawEnum::getAiModelName(DrawEnum::API_SD),
            DrawEnum::API_DALLE3    => DrawEnum::getAiModelName(DrawEnum::API_DALLE3),
            DrawEnum::API_MJ_GOAPI  => DrawEnum::getAiModelName(DrawEnum::API_MJ_GOAPI),
            'music'                 => 'AI音乐',
            'mindmap'               => '思维导图',
            'video'                 => 'AI视频',
            'aisearch'              => 'AI搜索'
        ];

        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }
}