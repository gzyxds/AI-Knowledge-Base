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

use app\common\model\chat\Models;

/**
 * AI视频枚举类
 */
class VideoEnum
{

    //来源
    const SOURCE_ADMIN = 1;//官方
    const SOURCE_USER = 2;//用户
    // 渠道
    const GOAPI    = 'go_api';
    const OPENAIHK = 'openai_hk';

    // 模型
    const LUMA = 'luma';

    // 状态
    const STATUS_WAIT           = 0; // 待处理
    const STATUS_IN_PROGRESS    = 1; // 执行中
    const STATUS_SUCCESS        = 2; // 成功
    const STATUS_FAIL           = 3; // 失败

    // 类型
    const TEXT_TO_VIDEO = 1; // 文本生成
    const IMAGE_TO_VIDEO = 2; // 图片生成

    // 提示词翻译API
    const TRANSLATE_BAIDU = 'baidu';
    const TRANSLATE_OPENAI = 'openai';
    const TRANSLATE_ZHIPU = 'zhipu';

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
            self::OPENAIHK  => 'OpenAi-HK(luma)',
            self::GOAPI     => 'GoAPI(luma)',
        ];
        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }

    /**
     * @notes 生成类型
     * @param bool|string $from
     * @return array|string
     * @author mjf
     * @date 2024/7/1 10:50
     */
    public static function getTypeDesc(bool|string $from = true): array|string
    {
        $desc = [
            self::TEXT_TO_VIDEO => '文生视频',
            self::IMAGE_TO_VIDEO => '图生视频',
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
            self::OPENAIHK => [
                'channel'       => self::OPENAIHK,
                'price'         => 200,
                'default'       => 1,
                'name'          => 'OpenAi-HK(luma)',
                'version'       => 'pro',
                'tips'          => '开通地址：https://www.openai-hk.com',
                'website'       => 'https://openai-hk.com/?i=33297',
            ],
            self::GOAPI => [
                'channel'       => self::GOAPI,
                'price'         => 200,
                'default'       => 1,
                'name'          => 'GoAPI(luma)',
                'version'       => '',
                'tips'          => '开通地址：https://www.goapi.ai',
                'website'       => 'https://dashboard.goapi.ai/?referrerId=e6d5f588-d5ff-4028-b606-7baf0f7fc915',
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

    // 翻译默认配置
    public static function getTranslateDefault()
    {
        return [
            'status'           => 0,
            'api'              => VideoEnum::TRANSLATE_BAIDU,
            'api_model'        => '',
            'prompt'           => '我会用任何语言和你交流，你只需将我的话翻译为英语，不要解释我的话或者回复其他信息，请立刻将我的话翻译返回，我的话是:{prompt}',
            'baidu_appid'      => '',
            'baidu_secret_key' => '',
            'api_list'         => VideoEnum::getTranslateConfig()
        ];
    }

    // 翻译配置
    public static function getTranslateConfig(array $translateApiList = []): array
    {
        $gpt35 = (new Models())
            ->where(['id' => 1])
            ->field(['id,type,channel,logo,name'])
            ->append(['models_lists'])
            ->findOrEmpty()->toArray();

        $zhipu = (new Models())
            ->where(['id' => 4])
            ->field(['id,type,channel,logo,name,is_system,is_enable'])
            ->append(['models_lists'])
            ->findOrEmpty()->toArray();

        return [
            [
                'model'      => self::TRANSLATE_BAIDU,
                'model_name' => '百度翻译',
                'model_list' => [],
                'prompt'     => $translateApiList[0]['prompt'] ?? '',
            ],
            [
                'model'      => $gpt35['channel'],
                'model_name' => $gpt35['name'],
                'model_list' => $gpt35['models_lists'],
                'prompt'     => $translateApiList[1]['prompt'] ?? '',
            ],
            [
                'model'      => $zhipu['channel'],
                'model_name' => $zhipu['name'],
                'model_list' => $zhipu['models_lists'],
                'prompt'     => $translateApiList[2]['prompt'] ?? '',
            ]
        ];
    }


}