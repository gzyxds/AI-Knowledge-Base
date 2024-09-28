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

namespace app\api\logic;

use app\common\enum\VoiceEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\ChatRecord;
use app\common\model\kb\KbDigital;
use app\common\model\kb\KbRobot;
use app\common\model\kb\KbRobotRecord;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\UploadService;
use app\common\service\voice\KdxfVoiceService;
use app\common\service\voice\OpenaiVoiceService;
use Exception;
use think\file\UploadedFile;

/**
 * 语音功能逻辑类
 */
class VoiceLogic extends BaseLogic
{
    /**
     * @notes 文字转语音
     * @param int $type     (类型: [1=普通对话, 2=机器人对话(默认), 3=闲时回复])
     * @param int $recordId (记录ID)
     * @param int $userId   (用户ID)
     * @return bool|array
     * @author fzr
     */
    public static function generate(int $type, int $recordId, int $userId): bool|array
    {
        try {
            // 验证配置
            self::_checkVoiceConfig(VoiceEnum::VOICE_OUTPUT);

            // 获取配置
            $t = VoiceEnum::VOICE_OUTPUT;
            $voiceConfig = self::_getVoiceConfig($t);
            $channelConfig = $voiceConfig['configs'][$voiceConfig['channel']];

            // 获取对话
            $chatRecords = self::_checkChatRecords($type, $recordId, $userId);
            if (!$chatRecords or !$chatRecords['reply']) {
                return [];
            }

            // 切换数字人的指定配音
            if (!empty($chatRecords['dubbing'])) {
                $channelConfig['pronounce'] = $chatRecords['dubbing'];
            }

            // 验证机器人
            if ($type === 2) {
                $pronounce = self::_checkRobotDigital($chatRecords['robot_id'], $voiceConfig);
                $channelConfig['pronounce'] = $pronounce;
            }

            // 处理生成存储音频路径
            $filename = md5($type.$chatRecords['id'].$voiceConfig['channel'].$channelConfig['pronounce'].$channelConfig['speed']).'.mp3';
            $saveDir  = config('project.voice_output.save_dir');
            $fileUrl  = $saveDir.'/'.$filename;

            // 判断文件是否已经存在
            $storageEngine = ConfigService::get('storage', 'default', 'local');
            if ($storageEngine === 'local') {
                if (file_exists($fileUrl)) {
                    return ['url'=>FileService::getFileUrl($fileUrl)];
                }
            } else {
                $url = FileService::getFileUrl($fileUrl);
                $headers = @get_headers($url);
                if($headers && str_contains($headers[0], '200')) {
                    return ['url'=>FileService::getFileUrl($fileUrl)];
                }
            }

            // 验证存储目录
            if (!file_exists($saveDir)) {
                mkdir($saveDir, 0775, true);
            }

            // 文件保存路径
            $saveFileDir = public_path() . $fileUrl;

            // 语音合成对象
            $replyContent = is_array($chatRecords['reply']) ? $chatRecords['reply'][0] : $chatRecords['reply'];
            switch ($voiceConfig['channel']) {
                case VoiceEnum::KDXF:
                    (new KdxfVoiceService())->voiceGenerate($channelConfig, $saveFileDir, $replyContent);
                    break;
                case VoiceEnum::OPENAI:
                    $binaryData = (new OpenaiVoiceService())->voiceGenerate($channelConfig, $replyContent);
                    file_put_contents($saveFileDir, $binaryData);
            }

            // 保存到OSS
            if('local' !== $storageEngine){
                UploadService::saveOssFile($fileUrl);
            }

            return ['url'=>FileService::getFileUrl($fileUrl)];
        } catch (Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 语音转文字
     * @param int $userId
     * @param UploadedFile $file
     * @return bool|array
     * @author fzr
     */
    public static function transfer(int $userId, UploadedFile $file): bool|array
    {
        try {
            // 验证配置
            self::_checkVoiceConfig(VoiceEnum::VOICE_INPUT);

            // 文件名称
            $fileName = md5(uniqid($userId).mt_rand(0, 99999).time()).'.mp3';
            $saveDir  = config('project.voice_input.save_dir') . '/'. date('Ymd');

            // 保存到本地
            $file->move($saveDir, $fileName);

            // 音频转文字
            $fileUrl =  $saveDir.'/'.$fileName;
            $channel = ConfigService::get('voice_input', 'channel', 'kdxf');
            $configs = ConfigService::get('voice_input', 'configs', []);

            // 调用转换类
            $fileRootPath = public_path() . $fileUrl;
            $transferText = match ($channel) {
                VoiceEnum::KDXF   => (new KdxfVoiceService())->voiceTransfer($fileRootPath),
                VoiceEnum::OPENAI => (new OpenaiVoiceService())->voiceTransfer($configs['openai'], $fileRootPath)
            };

            return [
                'text' => $transferText,
                'file' => FileService::getFileUrl($fileUrl)
            ];
        } catch (Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 验证语音播报配置
     * @param $type (语音类型）
     * @throws Exception
     * @author fzr
     */
    private static function _checkVoiceConfig($type): void
    {
        // 验证功能是否开启
        switch ($type){
            case VoiceEnum::VOICE_OUTPUT:
                $isOpen = ConfigService::get('voice_output','is_open');
                if (0 == $isOpen) {
                    throw new Exception('语音播报功能未开启');
                }
                break;
            case VoiceEnum::VOICE_INPUT:
                $isOpen = ConfigService::get('voice_input','is_open');
                if (0 == $isOpen) {
                    throw new Exception('语音输入功能未开启');
                }
                break;
            default:
                throw new Exception('语音类型参数错误');
        }
    }

    /**
     * @notes 验证对话记录数据
     * @param int $scene
     * @param int $recordId
     * @param int $userId
     * @return array
     * @throws Exception
     * @author fzr
     */
    private static function _checkChatRecords(int $scene, int $recordId, int $userId): array
    {
        if (!$recordId) {
            throw new Exception('请选择播报的对话');
        }

        switch ($scene) {
            case 1:
                return (new ChatRecord())
                    ->field(['id,reply'])
                    ->where(['id' => $recordId])
                    ->where(['user_id' => $userId])
                    ->findOrEmpty()
                    ->toArray();
            case 2:
                return (new KbRobotRecord())
                    ->field(['id,robot_id,reply'])
                    ->where(['id' => $recordId])
                    ->where(['user_id' => $userId])
                    ->findOrEmpty()
                    ->toArray();
            case 3:
                $kbRobot = (new KbRobot())->where(['id'=>$recordId])->findOrEmpty()->toArray();
                if ($kbRobot && $kbRobot['digital_id']) {
                    $digital = (new KbDigital())->where(['id'=>$kbRobot['digital_id']])->findOrEmpty()->toArray();
                    $idle_reply = $digital['idle_reply']??'';
                    return ['id'=>$digital['id'], 'reply'=>$idle_reply, 'dubbing'=>$digital['dubbing']];
                }
                return [];
        }

        if (empty($records)) {
            throw new Exception('对话记录不存在');
        }

        $reply = $records['reply'] ?? '';
        if (!$reply) {
            throw new Exception('对话内容不存在');
        }

        return $records;
    }

    /**
     * @notes 验证机器人数字人
     * @param int $robotId
     * @param array $voiceConfig
     * @return string
     * @throws Exception
     * @author fzr
     */
    private static function _checkRobotDigital(int $robotId, array $voiceConfig): string
    {
        $robot = (new KbRobot())->where(['id'=>$robotId])->findOrEmpty()->toArray();
        if (!$robot) {
            throw new Exception('当前机器人可能已丢失了');
        }

        if (!$robot['digital_id']) {
            throw new Exception('当前机器人尚未绑定数字人');
        }

        $digital = (new KbDigital())
            ->field(['id,channel,dubbing,is_disable'])
            ->where(['id'=>$robot['digital_id']])
            ->findOrEmpty()
            ->toArray();

        if (!$digital) {
            throw new Exception('当前应用绑定的数字人已丢失');
        }

        if ($digital['is_disable']) {
            throw new Exception('当前应用绑定的数字人已被禁用');
        }

        if ($voiceConfig['channel'] !== $digital['channel']) {
            throw new Exception('配音角色发生变化，请重新选择数字发音人');
        }

        $pronounceList = VoiceEnum::getOutputChannelDefaultConfig($voiceConfig['channel'])['pronounce_list'];
        $pronounceList = array_keys($pronounceList);
        if (!in_array($digital['dubbing'], $pronounceList)) {
            throw new Exception('配音角色发生变化，请重新选择数字发音人');
        }

        return $digital['dubbing'];
    }

    /**
     * @notes 获取语音的配置
     * @param int $type
     * @return array
     * @author fzr
     */
    private static function _getVoiceConfig(int $type): array
    {
        $voiceConfig  = [];
        switch ($type) {
            case VoiceEnum::VOICE_OUTPUT:
                $voiceConfig = [
                    'is_open'  => ConfigService::get('voice_output','is_open', 0),
                    'channel'  => ConfigService::get('voice_output','channel', VoiceEnum::KDXF),
                    'configs'  => ConfigService::get('voice_output', 'configs', []),
                ];
                if (!$voiceConfig['configs']) {
                    $config = VoiceEnum::getOutputChannelDefaultConfig();
                    foreach ($config as &$item) {
                        unset($item['pronounce_list']);
                        if (isset($item['model_list'])) {
                            unset($item['model_list']);
                        }
                    }
                    $voiceConfig['configs'] = $config;
                }
                break;
            case VoiceEnum::VOICE_INPUT:
                $voiceConfig = [
                    'is_open' => ConfigService::get('voice_input','is_open'),
                    'channel' => ConfigService::get('voice_input','channel'),
                    'configs' => []
                ];
                break;
        }

        return $voiceConfig;
    }
}