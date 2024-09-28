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

namespace app\common\service\voice;

use app\common\cache\KeyPoolCache;
use app\common\enum\PoolEnum;
use app\common\enum\VoiceEnum;
use CURLFile;
use Exception;
use WpOrg\Requests\Requests;

class OpenaiVoiceService
{
    /**
     * @notes 语音合成
     * @param array $channelConfig
     * @param string $content
     * @return string
     * @throws Exception
     * @author fzr
     */
    public function voiceGenerate(array $channelConfig, string $content): string
    {
        $keyPoolCache = (new KeyPoolCache(VoiceEnum::OPENAI,PoolEnum::TYPE_VOICE_OUTPUT, 'openai'));
        $apiKey = $keyPoolCache->getKey();
        if (empty($apiKey)) {
            throw new Exception('请在后台配置key');
        }

        // 参数
        $headers['Content-Type'] = 'application/json';
        $headers['Authorization'] = 'Bearer '.$apiKey;

        // 代理
        $agencyApi = 'https://api.openai.com';
        if($channelConfig['agency_api']){
            $agencyApi = $channelConfig['agency_api'];
        }

        $baseUrl = $agencyApi.'/v1/audio/speech';
        $data = [
            'model'           => $channelConfig['model'],
            'voice'           => $channelConfig['pronounce'],
            'speed'           => floatval($channelConfig['speed']),
            'input'           => $content,
            'response_format' => 'mp3'
        ];

        $options = [];
        $options['timeout'] = 20;
        $options['verify'] = false;
        $response = Requests::post($baseUrl, $headers, json_encode($data), $options);
        $responseData = json_decode($response->body,true);
        if (isset($responseData['error'])) {
            $keyPoolCache->takeDownKey($responseData['error']['message'] ?? '', $baseUrl);
            throw new Exception($responseData['error']['message'] ? : $responseData['error']['type']);
        }
        return $response->body;
    }

    /**
     * @notes 语音转写
     * @param array $channelConfig
     * @param string $fileUrl
     * @return mixed
     * @throws Exception
     * @author fzr
     */
    public function voiceTransfer(array $channelConfig,string $fileUrl): mixed
    {
        $keyPoolCache = (new KeyPoolCache(VoiceEnum::OPENAI,PoolEnum::TYPE_VOICE_INPUT));
        $apiKey = $keyPoolCache->getKey();
        if(empty($apiKey)){
            throw new Exception('请在后台配置key');
        }

        $header = [
            'Content-Type:multipart/form-data',
            'Authorization: Bearer '.$apiKey,
        ];
        $agencyApi = 'https://api.openai.com';
        if(!empty($channelConfig['agency_api'])){
            $agencyApi = $channelConfig['agency_api'];
        }
        $baseUrl = $agencyApi.'/v1/audio/transcriptions';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'file'  => new CURLFile($fileUrl),
                'language'  =>'zh',
                'prompt'    => '如翻译的内容是中文，请将内容翻译成简体中文',
                'model' => 'whisper-1'
            ]
        ]);
        $data = curl_exec($curl);
        $response = json_decode($data,true);

        if(isset($response['error'])){
            $keyPoolCache->takeDownKey($response['error']['message'] ?? '',$baseUrl);
            throw new Exception($response['error']['message'] ? : $response['error']['type']);
        }
        return $response['text'];
    }
}