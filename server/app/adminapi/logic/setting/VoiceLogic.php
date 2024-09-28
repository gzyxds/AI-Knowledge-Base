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

namespace app\adminapi\logic\setting;

use app\common\enum\VoiceEnum;
use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

/**
 * 语音配置逻辑类
 */
class VoiceLogic extends BaseLogic
{
    /**
     * @notes 获取语音播报配置
     * @return array
     * @author fzr
     */
    public static function detail(): array
    {
        $outputChannelConfig = VoiceEnum::getOutputChannelDefaultConfig();
        $inputChannelConfig  = VoiceEnum::getInputChannelDefaultConfig();
        $voiceInputChannel   = [];
        $voiceOutputChannel  = [];

        // 语音输出通道配置
        $outputConfig = ConfigService::get('voice_output', 'configs', []);
        foreach ($outputChannelConfig as $key => $defaultConfig) {
            $config = $outputConfig[$key]??[];
            $config = array_merge($defaultConfig, $config);
            $config['speed'] = VoiceEnum::KDXF == $key ? (int)$config['speed'] : (float)$config['speed'];
            $voiceOutputChannel[$key] = $config;
        }

        // 语音输入通道配置
        $inputConfig = ConfigService::get('voice_input', 'configs', []);
        foreach ($inputChannelConfig as $key => $defaultConfig) {
            $config = $inputConfig[$key]??[];
            $config = array_merge($defaultConfig, $config);
            $voiceInputChannel[$key] = $config;
        }

        return [
            'voice_input' => [
                'is_open'        => ConfigService::get('voice_input','is_open', 0),
                'channel'        => ConfigService::get('voice_input','channel', 'kdxf'),
                'channel_config' => $voiceInputChannel
            ],
            'voice_output' => [
                'is_open'        => ConfigService::get('voice_output','is_open', 0),
                'channel'        => ConfigService::get('voice_output','channel', 'kdxf'),
                'channel_config' => $voiceOutputChannel,
            ]
        ]??[];
    }

    /**
     * @notes 设置语音播报
     * @param array $post
     * @author fzr
     */
    public static function save(array $post): void
    {
        $inputConfig  = $post['voice_input']['channel_config'];
        $outputConfig = $post['voice_output']['channel_config'];

        // 语音输入
        ConfigService::set('voice_input', 'is_open', $post['voice_input']['is_open']);
        ConfigService::set('voice_input', 'channel', $post['voice_input']['channel']);
        ConfigService::set('voice_input', 'configs', [
            'kdxf' => [
                'name'      => $inputConfig['kdxf']['name'],
                'speed'     => $inputConfig['kdxf']['speed'],
                'pronounce' => $inputConfig['kdxf']['pronounce']
            ],
            'openai' => [
                'name'       => $inputConfig['openai']['name'],
                'model'      => $inputConfig['openai']['model'],
                'agency_api' => $inputConfig['openai']['agency_api'],
            ]
        ]);

        // 语音输出
        ConfigService::set('voice_output', 'is_open', $post['voice_output']['is_open']);
        ConfigService::set('voice_output', 'channel', $post['voice_output']['channel']);
        ConfigService::set('voice_output', 'configs', [
            'kdxf' => [
                'name'      => $outputConfig['kdxf']['name'],
                'speed'     => $outputConfig['kdxf']['speed'],
                'pronounce' => $outputConfig['kdxf']['pronounce']
            ],
            'openai' => [
                'name'       => $outputConfig['openai']['name'],
                'model'      => $outputConfig['openai']['model'],
                'speed'      => $outputConfig['openai']['speed'],
                'pronounce'  => $outputConfig['openai']['pronounce'],
                'agency_api' => $outputConfig['openai']['agency_api']??'',
            ]
        ]);
    }
}