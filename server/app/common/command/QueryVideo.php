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

use app\common\enum\user\AccountLogEnum;
use app\common\enum\VideoEnum;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\model\video\VideoRecord;
use app\common\service\ConfigService;
use app\common\service\video\GoApiService;
use app\common\service\video\OpenaiHKService;
use app\common\service\storage\Driver as StorageDriver;
use Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Cache;
use think\facade\Log;

class QueryVideo extends Command
{
    protected function configure(): void
    {
        $this->setName('query_video')
            ->setDescription('视频查询处理');
    }

    protected function execute(Input $input, Output $output): bool
    {
        try {
            $recordModel = new VideoRecord();
            $records     = $recordModel
                ->where(['status' => VideoEnum::STATUS_IN_PROGRESS])
                ->limit(5)
                ->select()
                ->toArray();

            if (empty($records)) {
                return false;
            }

            foreach ($records as $record) {
                match ($record['channel']) {
                    VideoEnum::OPENAIHK => $this->queryOpenaiHk($record),
                    VideoEnum::GOAPI => $this->queryGoApi($record),
                };
            }

            return true;
        } catch (Exception $e) {
            Log::write('视频任务查询失败:' . $e->getMessage());
            return false;
        }
    }

    /**
     * @notes 查询处理（OpenAiHk渠道）
     * @param $record
     * @throws Exception
     * @author mjf
     * @date 2024/6/21 18:33
     */
    protected function queryOpenaiHk($record)
    {
        try {
            $lockKey  = "video_task_" . $record['id'];
            $lockData = Cache::get($lockKey);
            if (!empty($lockData)) {
                return;
            }

            $service  = new OpenaiHKService($record['api_version']);
            $response = $service->query($record['task_id']);

            if (!empty($response['state']) && $response['state'] == 'failed') {
                $this->failHandle($record, $response, $response['error_message'] ?? '生成失败');
                return;
            }

            if (!empty($response['state']) && $response['state'] == 'completed') {
                // 锁定记录
                Cache::set($lockKey, json_encode($record, JSON_UNESCAPED_UNICODE), 180);

                // 更新任务状态
                $downloadUrl = $response['video']['download_url'] ?? '';
                $downloadUrl = empty($downloadUrl) ? $response['video']['url'] : $downloadUrl;
                $videoUrl    = $this->downloadFile($downloadUrl);

                $data = [
                    'response'    => json_encode($response, JSON_UNESCAPED_UNICODE),
                    'video_url'   => $videoUrl,
                    'status'      => VideoEnum::STATUS_SUCCESS,
                    'update_time' => time()
                ];
                VideoRecord::update($data, ['id' => $record['id']]);

                // 删除锁定
                Cache::delete($lockKey);
            } else {
                $this->taskFail($record, $response);
            }

        } catch (Exception $e) {
            Cache::delete($lockKey);
            $this->taskFail($record, [], $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 查询处理（GoApi渠道）
     * @param $record
     * @throws Exception
     * @author mjf
     * @date 2024/7/11 16:39
     */
    protected function queryGoApi($record)
    {
        try {
            $lockKey  = "video_task_" . $record['id'];
            $lockData = Cache::get($lockKey);
            if (!empty($lockData)) {
                return;
            }

            $service  = new GoApiService();
            $response = $service->query($record['task_id']);
            unset($response['input']);

            if (!empty($response['status']) && $response['status'] == 'completed') {
                // 锁定记录
                Cache::set($lockKey, json_encode($record, JSON_UNESCAPED_UNICODE), 180);

                // 更新任务状态
                $videoUrl = $this->downloadFile($response['generation']['video']['url'] ?? '');

                $data = [
                    'response'    => json_encode($response, JSON_UNESCAPED_UNICODE),
                    'video_url'   => $videoUrl,
                    'status'      => VideoEnum::STATUS_SUCCESS,
                    'update_time' => time()
                ];
                VideoRecord::update($data, ['id' => $record['id']]);

                // 删除锁定
                Cache::delete($lockKey);
            } else {
                $this->taskFail($record, $response);
            }

        } catch (Exception $e) {
            Cache::delete($lockKey);
            $this->taskFail($record, [], $e->getMessage());
            throw new Exception($e->getMessage());
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
        $timeout             = 10 * 60;
        $task['create_time'] = is_string($task['create_time'])
            ? strtotime($task['create_time'])
            : $task['create_time'];

        if (time() - $timeout > $task['create_time']) {
            $this->failHandle($task, $response, $error);
        }
    }

    /**
     * @notes 错误处理
     * @param $record
     * @param $response
     * @param string $error
     * @author mjf
     * @date 2024/6/11 18:24
     */
    protected function failHandle($record, $response, string $error = '超时响应')
    {
        VideoRecord::update([
            'fail_reason' => $error,
            'response'    => json_encode($response, JSON_UNESCAPED_UNICODE),
            'status'      => VideoEnum::STATUS_FAIL,
            'update_time' => time()
        ], ['id' => $record['id']]);

        // 回退用户金额
        $this->backBalance($record['user_id'], $record['tokens']);
    }

    /**
     * @notes 下载文件
     * @param $downloadUrl
     * @param string $saveDir
     * @return string
     * @author mjf
     * @date 2024/5/30 11:25
     */
    protected function downloadFile($downloadUrl, string $saveDir = 'uploads/video/'): string
    {
        if (empty($downloadUrl)) {
            return '';
        }

        try {
            // 存储引擎
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage')
            ];

            // 文件名称
            $fileName = $this->imageUrlTrim($downloadUrl);

            // 第三方存储
            if ($config['default'] == 'local') {
                $downSaveDir = app()->getRootPath() . 'public/' . $saveDir;
                $downRes     = download_file($downloadUrl, $downSaveDir . date('Ymd') . '/', $fileName, false);
                if (empty($downRes)) {
                    throw new Exception('下载本地失败,地址-' . $downloadUrl);
                }
                return $saveDir . date('Ymd') . '/' . $fileName;
            } else {
                $localPath     = $saveDir . date('Ymd') . '/' . $fileName;
                $StorageDriver = new StorageDriver($config);
                if (!$StorageDriver->fetch($downloadUrl, $localPath)) {
                    throw new Exception('上传oss失败:' . $StorageDriver->getError() . $downloadUrl);
                }
                return $localPath;
            }

        } catch (\Exception $e) {
            Log::write('视频资源下载:' . $e->getMessage() . $e->getLine());
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
        $changeType   = AccountLogEnum::UM_INC_VIDEO_FAIL;
        $changeAction = AccountLogEnum::INC;
        UserAccountLog::add($userId, $changeType, $changeAction, $price);
    }

    /**
     * @notes 图片文件名
     * @param $image
     * @return string
     * @author mjf
     * @date 2024/7/11 14:11
     */
    public function imageUrlTrim($image)
    {
        if (str_contains($image, '?')) {
            $image = strtok($image, '?');
        }
        return basename($image);
    }

}