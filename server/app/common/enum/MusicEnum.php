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
declare (strict_types=1);

namespace app\common\enum;

/**
 * AI音乐枚举类
 */
class MusicEnum
{

    // 音乐模型
    const SUNO = 'suno';

    // 音乐渠道
    const GOAPI = 'go_api';
    const OPENAIHK = 'openai_hk';

    // 版本
    const CHIRP_V30 = 'chirp-v3-0';
    const CHIRP_V35 = 'chirp-v3-5';

    // 状态
    const STATUS_WAIT           = 0; // 待处理
    const STATUS_IN_PROGRESS    = 1; // 执行中
    const STATUS_SUCCESS        = 2; // 成功
    const STATUS_FAIL           = 3; // 失败

    /**
     * @notes 获取模型
     * @param bool|string $from
     * @return array|string
     * @author mjf
     * @date 2024/5/27 14:19
     */
    public static function getChannel(bool|string $from = true): array|string
    {
        $desc = [
            self::GOAPI     => 'GoAPI',
            self::OPENAIHK  => 'OpenAi-HK',
        ];
        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }


    /**
     * @notes 默认配置
     * @param bool $from
     * @return array
     * @author mjf
     * @date 2024/5/27 17:23
     */
    public static function getChannelDefaultConfig(bool|string $from = true): array
    {
        $desc = [
            self::GOAPI => [
                'channel'       => self::GOAPI,
                'price'         => 10,
                'default'       => 1,
                "name"          => "GoAPI",
                "version"       => [self::CHIRP_V30],
                'tips'          => '开通地址：https://www.goapi.ai/suno-api',
                'website'       => 'https://dashboard.goapi.ai/?referrerId=e6d5f588-d5ff-4028-b606-7baf0f7fc915',
            ],
            self::OPENAIHK => [
                'channel'       => self::OPENAIHK,
                'price'         => 10,
                'default'       => 1,
                "name"          => "OpenAi-HK",
                "version"       => [self::CHIRP_V30],
                'tips'          => '开通地址：https://www.openai-hk.com',
                'website'       => 'https://openai-hk.com/?i=33297',
            ],
        ];

        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? [];
    }

    /**
     * @notes 状态
     * @param bool|int $value
     * @return string|string[]
     * @author mjf
     * @date 2024/5/30 15:18
     */
    public static function getStatusDesc(bool|int $value = true): array|string
    {
        $data = [
            self::STATUS_WAIT           => '待处理',
            self::STATUS_IN_PROGRESS    => '生成中',
            self::STATUS_SUCCESS        => '生成成功',
            self::STATUS_FAIL           => '生成失败',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }

    /**
     * @notes 音乐版本
     * @return array|string
     * @author mjf
     * @date 2024/7/1 18:43
     */
    public static function getVersion(string|bool $value = true): array|string
    {
        $data =  [
            self::CHIRP_V30 => 'v3.0',
            self::CHIRP_V35 => 'v3.5',
        ];

        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }


}