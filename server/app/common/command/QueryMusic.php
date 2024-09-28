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

namespace app\common\command;

use app\common\enum\MusicEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\model\music\MusicRecord;
use app\common\model\music\MusicTask;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\service\ConfigService;
use app\common\service\music\GoApiService;
use app\common\service\music\OpenaiHKService;
use app\common\service\storage\Driver as StorageDriver;
use Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

class QueryMusic extends Command
{
    protected string $imageSavePath = 'uploads/images/';
    protected string $videoSavePath = 'uploads/video/';
    protected string $audioSavePath = 'uploads/voice/audio/';

    protected function configure(): void
    {
        $this->setName('query_music')
            ->setDescription('音乐查询处理');
    }

    protected function execute(Input $input, Output $output): bool
    {
        try {
            $taskModel = new MusicTask();
            $tasks = $taskModel
                ->where(['status' => MusicEnum::STATUS_IN_PROGRESS])
                ->order('id desc')
                ->limit(5)
                ->select()
                ->toArray();

            if (empty($tasks)) {
                return false;
            }

            foreach ($tasks as $task) {
                match ($task['channel']) {
                    MusicEnum::GOAPI => $this->queryGoApiHandle($task),
                    MusicEnum::OPENAIHK => $this->queryOpenaiHk($task),
                };
            }

            return true;
        } catch (Exception $e) {
            Log::write('音乐任务查询失败:' . $e->getMessage());
            return false;
        }
    }

    /**
     * @notes 查询处理（GoApi渠道）
     * @param $task
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws Exception
     * @author mjf
     * @date 2024/5/30 14:34
     */
    protected function queryGoApiHandle($task)
    {
        $response = [];
        try {
            $service = new GoApiService();
            $response = $service->query($task['task_id']);
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'task not found')) {
                $this->taskFail($task, $response, $e->getMessage());
            } else {
                throw new Exception($e->getMessage());
            }
        }

        if (!empty($response['status']) && $response['status'] == 'completed') {
            $clips = array_values($response['clips'] ?? []);
            if (empty($clips)) {
                return;
            }

            $recordModel = new MusicRecord();
            $records = $recordModel->where('task_id', $task['id'])
                ->where('status', MusicEnum::STATUS_IN_PROGRESS)
                ->limit(2)
                ->select()
                ->toArray();

            if (!$this->isAllComplete($clips)) {
                return;
            }

            // 更新任务状态
            MusicTask::update([
                'response'      => json_encode($response, JSON_UNESCAPED_UNICODE),
                'status'        => MusicEnum::STATUS_SUCCESS,
                'update_time'   => time()
            ], ['id' => $task['id']]);

            foreach ($records as $key => $record) {
                if (empty($clips[$key])) {
                    continue;
                }

                $imageFile = $this->downloadFile($clips[$key]['image_url'] ?? '', $this->imageSavePath);
                $imageLargeFile = $this->downloadFile($clips[$key]['image_large_url'] ?? '', $this->imageSavePath);
                $audioFile = $this->downloadFile($clips[$key]['audio_url'] ?? '', $this->audioSavePath);
                $videoFile = $this->downloadFile($clips[$key]['video_url'] ?? '', $this->videoSavePath);

                $data = [
                    'clips_id'          => $clips[$key]['id'] ?? '',
                    'lyric'             => $clips[$key]['metadata']['prompt'] ?? '',
                    'duration'          => $clips[$key]['metadata']['duration'] ?? 0,
                    'image_url'         => $imageFile,
                    'image_large_url'   => $imageLargeFile,
                    'audio_url'         => $audioFile,
                    'video_url'         => $videoFile,
                    'status'            => MusicEnum::STATUS_SUCCESS,
                    'update_time'       => time()
                ];
                MusicRecord::update($data, ['id' => $record['id']]);
            }
        } elseif (!empty($response['status']) && $response['status'] == 'failed') {
            $this->failHandle($task, $response, $response['error_message'] ?? '生成失败');
        } else {
            $this->taskFail($task, $response);
        }
    }

    /**
     * @notes 查询处理（OpenAiHk渠道）
     * @param $task
     * @throws Exception
     * @author mjf
     * @date 2024/6/21 18:33
     */
    protected function queryOpenaiHk($task)
    {
        $response = [];
        try {
            $recordModel = new MusicRecord();
            $records = $recordModel->where('task_id', $task['id'])
                ->where('status', MusicEnum::STATUS_IN_PROGRESS)
                ->limit(2)
                ->select()
                ->toArray();

            $clipsIds = array_column($records, 'clips_id');
            $clipsIds = implode(',', $clipsIds);

            if (empty($clipsIds)) {
                return;
            }

            $service = new OpenaiHKService();
            $response = $service->query($clipsIds);

            if (!$this->isAllComplete($response)) {
                if (!empty($response[0]['status']) && $response[0]['status'] == 'error') {
                    $this->failHandle($task, $response, $response[0]['metadata']['error_message'] ?? '生成失败');
                } else {
                    $this->taskFail($task, $response);
                }
                return;
            }

            // 更新任务状态
            MusicTask::update([
                'response'      => json_encode($response, JSON_UNESCAPED_UNICODE),
                'status'        => MusicEnum::STATUS_SUCCESS,
                'update_time'   => time()
            ], ['id' => $task['id']]);

            foreach ($response as $clips) {
                if (!empty($clips['status']) && $clips['status'] == 'complete') {
                    $imageFile = $this->downloadFile($clips['image_url'] ?? '', $this->imageSavePath);
                    $imageLargeFile =  $this->downloadFile($clips['image_large_url'] ?? '', $this->imageSavePath);
                    $audioFile = $this->downloadFile($clips['audio_url'] ?? '', $this->audioSavePath);
                    $videoFile = $this->downloadFile($clips['video_url'] ?? '', $this->videoSavePath);

                    $data = [
                        'lyric'             => $clips['metadata']['prompt'] ?? '',
                        'duration'          => $clips['metadata']['duration'] ?? 0,
                        'image_url'         => $imageFile,
                        'image_large_url'   => $imageLargeFile,
                        'audio_url'         => $audioFile,
                        'video_url'         => $videoFile,
                        'status'            => MusicEnum::STATUS_SUCCESS,
                        'update_time'       => time()
                    ];
                    MusicRecord::update($data, ['clips_id' => $clips['id']]);
                }
            }

        } catch (Exception $e) {
            $this->taskFail($task, $response, $e->getMessage());
        }
    }

    /**
     * @notes 任务失败
     * @param $task
     * @param $response
     * @param string $error
     * @author mjf
     * @date 2024/5/30 14:22
     */
    protected function taskFail($task, $response, string $error = '超时响应')
    {
        // 查询失败且时间过长则关闭
        $timeout = 10 * 60;
        $task['create_time'] = is_string($task['create_time'])
            ? strtotime($task['create_time'])
            : $task['create_time'];

        if (time() - $timeout > $task['create_time']) {
            $this->failHandle($task, $response, $error);
        }
    }

    /**
     * @notes 错误处理
     * @param $task
     * @param $response
     * @param string $error
     * @author mjf
     * @date 2024/6/11 18:24
     */
    protected function failHandle($task, $response, string $error = '超时响应')
    {
        MusicTask::update([
            'fail_reason'   => $error,
            'response'      => json_encode($response, JSON_UNESCAPED_UNICODE),
            'status'        => MusicEnum::STATUS_FAIL,
            'update_time'   => time()
        ], ['id' => $task['id']]);

        MusicRecord::update([
            'fail_reason'   => $error,
            'response'      => json_encode($response, JSON_UNESCAPED_UNICODE),
            'status'        => MusicEnum::STATUS_FAIL,
            'update_time'   => time()
        ], ['task_id' => $task['id']]);

        // 回退用户金额
        $this->backBalance($task['user_id'], $task['tokens']);
    }

    /**
     * @notes 下载文件
     * @param $downloadUrl
     * @param string $saveDir
     * @return string
     * @author mjf
     * @date 2024/5/30 11:25
     */
    protected function downloadFile($downloadUrl, string $saveDir = 'uploads/images/'): string
    {
        if (empty($downloadUrl)) {
            return '';
        }

        try {
            // 存储引擎
            $config = [
                'default'   => ConfigService::get('storage', 'default', 'local'),
                'engine'    => ConfigService::get('storage')
            ];

            // 文件名称
            $fileName = basename($downloadUrl);

            $localSaveDir = app()->getRootPath() . 'public/' . $saveDir;
            $dateDir = date('Ymd'). '/';
            $downRes = $this->downloadHandle($downloadUrl, $localSaveDir . $dateDir, $fileName);
            if (empty($downRes)) {
                throw new Exception('下载本地失败,图片地址-' . $downloadUrl);
            }

            if ($config['default'] != 'local') {
                $localSaveFullPath = $localSaveDir . $dateDir . $fileName;
                $ossSavePath = $saveDir . $dateDir . $fileName;
                $StorageDriver = new StorageDriver($config);
                if (!$StorageDriver->fetch($localSaveFullPath, $ossSavePath)) {
                    throw new Exception('上传oss失败:' . $StorageDriver->getError() . $localSaveFullPath);
                }
                unlink($localSaveFullPath);
            }
            return $saveDir . $dateDir . $fileName;

        } catch (\Exception $e) {
            Log::write('音乐资源:' . $e->getMessage() . $e->getLine());
            return "";
        }
    }

    /**
     * @notes 回退余额
     * @param $price
     * @param $userId
     * @author mjf
     * @date 2024/5/30 11:50
     */
    protected function backBalance($userId, $price)
    {
        $user = (new User())->where('id', $userId)->findOrEmpty();

        if ($price <= 0) {
            return;
        }

        $user->balance = $user['balance'] + $price;
        $user->save();

        // 钱包变动
        $changeType = AccountLogEnum::UM_INC_MUSIC_FAIL;
        $changeAction = AccountLogEnum::INC;
        UserAccountLog::add($userId, $changeType, $changeAction, $price);
    }


    /**
     * @notes clips状态
     * @param $array
     * @return bool
     * @author mjf
     * @date 2024/6/11 17:46
     */
    protected function isAllComplete($array): bool
    {
        $statuses = array_map(function ($item) {
            return $item['status'] === 'complete';
        }, $array);
        return !in_array(false, $statuses, true);
    }


    public function downloadHandle($url, $saveDir, $fileName): string
    {
        if (!file_exists($saveDir)) {
            mkdir($saveDir, 0775, true);
        }

        $fileSrc = $saveDir . $fileName;
        file_exists($fileSrc) && unlink($fileSrc);

        // 设置请求头
        $headers = [
            'Referer: https://suno.com/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        ];

        $options = [
            'http' => [
                'header' => implode("\r\n", $headers),
            ]
        ];

        $fileContent = file_get_contents($url, false, stream_context_create($options));
        if ($fileContent === false) {
            return "";
        }

        $bytesWritten = file_put_contents($fileSrc, $fileContent);
        if ($bytesWritten === false) {
            return "";
        }

        return $fileSrc;
    }
}