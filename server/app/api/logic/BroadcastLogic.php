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
use app\common\model\kb\KbRobotPublish;
use app\common\model\kb\KbRobotRecord;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\UploadService;
use app\common\service\voice\KdxfVoiceService;
use app\common\service\voice\OpenaiVoiceService;
use Exception;

/**
 * 语音播报逻辑类
 */
class BroadcastLogic extends BaseLogic
{
    /**
     * @notes 生成语音
     * @param array $params
     * @param int $userId
     * @return bool|array
     * @author fzr
     */
    public function generate(array $params, int $userId): bool|array
    {
        try {
            // 基础参数
            $recordsId = $params['records_id'] ?? 0;
            $content   = $params['content']    ?? 0;
            $type      = $params['type']       ?? 1;
            // 机器人分享专属参数
            $apiKey   = $params['apiKey']??'';
            $identity = $params['identity']??'';
            if ($apiKey) {
                $type = 2;
            }

            // 验证语音播报配置
            $records = $this->checkBradCast($userId, $type, $recordsId, $content, $apiKey, $identity);

            $channel       = ConfigService::get('voice_output', 'channel');
            $channelConfig = ConfigService::get('voice_output', 'configs', [])[$channel]??[];
            $defaultConfig = VoiceEnum::getOutputChannelDefaultConfig($channel);
            $channelConfig = array_merge($defaultConfig, $channelConfig);
            $channelConfig['channel'] = $channel;

            // 生成文件路径
            $fileDirInfo = $this->generateFileDir($type, $records, $content, $channelConfig);

            // 存储引擎
            $config = [
                'default'  => ConfigService::get('storage', 'default', 'local'),
                'engine'   => ConfigService::get('storage'),
                'file_url' => $fileDirInfo['save_file_dir'],
            ];

            // 获取语音文件,如果之前生成过,不用重新生成
            $voiceFile = $this->getVoiceFile($config);
            if ($voiceFile) {
                return $voiceFile;
            }

            // 验证存储目录
            $saveDir = public_path() . $fileDirInfo['save_dir'];
            if (!file_exists($saveDir)) {
                mkdir($saveDir, 0775, true);
            }

            // 文件保存路径
            $saveFileDir = public_path() . $fileDirInfo['save_file_dir'];

            // 生成语音
            $replyContent = is_array($records['reply']) ? $records['reply'][$content] : $records['reply'];
            switch ($channel) {
                case VoiceEnum::KDXF:
                    (new KdxfVoiceService())->voiceGenerate($channelConfig, $saveFileDir, $replyContent);
                    break;
                case VoiceEnum::OPENAI:
                    $binaryData = (new OpenaiVoiceService())->voiceGenerate($channelConfig, $replyContent);
                    file_put_contents($saveFileDir, $binaryData);
            }

            // 保存到本地或OSS
            if('local' !== $config['default']){
                UploadService::saveOssFile($fileDirInfo['save_file_dir']);
            }

            return [
                'file' => FileService::getFileUrl($fileDirInfo['save_file_dir'])
            ];
        }catch (Exception $e){
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 验证语音播报配置
     * @param int $userId
     * @param int $type
     * @param int $recordsId
     * @param int $content
     * @param string $apiKey
     * @param $identity
     * @return array
     * @throws Exception
     * @author cjhao
     * @date 2023/10/11 11:31
     */
    public function checkBradCast(int $userId, int $type, int $recordsId, int $content, string $apiKey, $identity) :array
    {
        if (empty($recordsId)) {
            throw new Exception('请选择播报的对话');
        }

        if (1 == $type) {
            $records = (new ChatRecord())->where(['user_id'=>$userId, 'id'=>$recordsId])->findOrEmpty()->toArray();
        } else {
            if ($apiKey) {
                $modelPublish = new KbRobotPublish();
                $publish = $modelPublish->where(['apiKey'=>$apiKey])->findOrEmpty()->toArray();
                $records = (new KbRobotRecord())->where(['id'=>$recordsId, 'share_id'=>$publish['id']??0, 'share_identity'=>$identity])->findOrEmpty()->toArray();
            } else {
                $userId = $userId ?: 0;
                $records = (new KbRobotRecord())->where(['user_id' => $userId, 'id' => $recordsId])->findOrEmpty()->toArray();
            }
        }

        if (empty($records)) {
            throw new Exception('对话不存在');
        }

        $reply = $records['reply'][$content] ?? '';
        if(empty($reply)){
            throw new Exception('对话内容不存在');
        }

        $isOpen = ConfigService::get('voice_output','is_open');
        if (0 == $isOpen) {
            throw new Exception('语音播报功能未开启');
        }

        return $records;
    }

    /**
     * @notes 生成语音文件路径
     * @param array $records
     * @param int $content
     * @param int $type
     * @param array $channelConfig
     * @return array
     * @author cjhao
     * @date 2023/10/11 11:21
     */
    public function generateFileDir(int $type,array $records,int $content,array $channelConfig):array
    {
        // 用id、配置生成md5文件名,后面通过md5判断以前是否生成过直接返回,不用在重新生成
        $filename = md5($type.$records['id'].$content.$channelConfig['pronounce'].$channelConfig['speed']).'.mp3';
        $saveDir  = config('project.voice_output.save_dir');
        return [
            'save_dir'      => $saveDir,
            'filename'      => $filename,
            'save_file_dir' => $saveDir.'/'.$filename,
        ]??[];
    }

    /**
     * @notes 获取语音文件
     * @param $config
     * @return array|bool
     * @author cjhao
     * @date 2023/10/11 11:30
     */
    public function getVoiceFile($config): bool|array
    {
        if ($config['default'] == 'local') {
            if(file_exists($config['file_url'])){
                return [
                    'file' => FileService::getFileUrl($config['file_url'])
                ];
            }
        } else {
            // 判断远程文件是否存在
            $fileUrl = FileService::getFileUrl($config['file_url']);
            if($this->getRemoteFileExists($fileUrl)){
                return [
                    'file' => $fileUrl
                ];
            }
        }
        return false;
    }

    /**
     * @notes 判断远程文件是否存在
     * @param string $url
     * @return bool
     * @author cjhao
     * @date 2023/10/11 9:52
     */
    private function getRemoteFileExists(string $url): bool
    {
        $headers = @get_headers($url);
        if ($headers && str_contains($headers[0], '200')) {
            return true;
        }
        return false;
    }
}