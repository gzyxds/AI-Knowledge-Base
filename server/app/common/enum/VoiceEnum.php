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

namespace app\common\enum;

/**
 * 语音播报枚举类
 */
class VoiceEnum
{
    const VOICE_OUTPUT = 1;
    const VOICE_INPUT  = 2;

    const KDXF   = 'kdxf';
    const OPENAI = 'openai';

    /**
     * @notes 获取发音人渠道 (讯飞)
     * @param bool $form
     * @return array|string
     */
    public static function getKdxfPronounceList(bool|string $form = true): array|string
    {
        $desc = [
            'x4_lingxiaoxuan_oral' => '聆小璇',
            'x4_lingfeizhe_oral'   => '聆飞哲',
            'x4_lingyuzhao_oral'   => '聆玉昭'
        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? '';

    }

    /**
     * @notes 获取发音人渠道 (OpenID)
     * @param bool $form
     * @return array|string
     */
    public static function getOpenAiPronounceList(bool|string $form = true): array|string
    {
        $desc = [
            'alloy'   => 'alloy',
            'echo'    => 'echo',
            'fable'   => 'fable',
            'onyx'    => 'onyx',
            'nova'    => 'nova',
            'shimmer' => 'shimmer'
        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? '';
    }

    /**
     * @notes 获取通道
     * @param bool $form
     * @return array|string
     */
    public static function getChannel(bool|string $form = true): array|string
    {
        $desc = [
            self::KDXF   => '科大讯飞',
            self::OPENAI => 'openAi-TTS'
        ];
        if (true === $form) {
            return $desc;
        }
        return $desc[$form] ?? '';
    }

    /**
     * @notes 获取"语音输入"渠道配置
     * @param bool $form
     * @return array
     * @author fzr
     */
    public static function getInputChannelDefaultConfig(bool|string $form = true): array
    {
        $desc = [
            self::KDXF  => [
                'name'           => '科大讯飞',
                'speed'          => 50,
                'pronounce'      => 'x4_lingxiaoxuan_oral',
                'pronounce_list' => self::getKdxfPronounceList(),
            ],
            self::OPENAI    => [
                'name'  => 'openAi-TTS',
                'model' => 'whisper-1',
                'agency_api' => '',
                'model_list'  => [
                    'whisper-1' => 'whisper-1'
                ],
            ]
        ];
        if(true === $form){
            return $desc;
        }
        return $desc[$form] ?? [];
    }

    /**
     * @notes 获取"语音输出"渠道配置
     * @param bool $form
     * @return array
     * @author fzr
     */
    public static function getOutputChannelDefaultConfig(bool|string $form = true): array
    {
        $desc = [
            self::KDXF  => [
                'name'           => '科大讯飞',
                'pronounce_list' => self::getKdxfPronounceList(),
                'pronounce'      => 'x4_lingxiaoxuan_oral',
                'speed'          => 50,
            ],
            self::OPENAI    => [
                'name'           => 'openAi-TTS',
                'pronounce_list' => self::getOpenAiPronounceList(),
                'pronounce'      => 'alloy',
                'speed'          => 1.0,
                'model'          => 'tts-1',
                'agency_api'     => '',
                'model_list'  => [
                    'tts-1'     => 'tts-1',
                    'tts-1-hd'  => 'tts-1-hd'
                ]
            ],
        ];

        if (true === $form) {
            return $desc;
        }
        return $desc[$form] ?? [];
    }
}