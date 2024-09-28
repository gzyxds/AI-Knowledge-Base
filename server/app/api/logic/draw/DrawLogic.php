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

namespace app\api\logic\draw;

use app\common\enum\ChatRecordEnum;
use app\common\enum\draw\DrawEnum;
use app\common\enum\draw\DrawRecordEnum;
use app\common\enum\draw\DrawTaskEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\logic\UserMemberLogic;
use app\common\model\draw\DrawModel;
use app\common\model\draw\DrawModelCategory;
use app\common\model\draw\DrawRecords;
use app\common\model\draw\DrawRecordsCollect;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\service\BaiduFanyiService;
use app\common\service\ConfigService;
use app\common\service\draw\DrawDriver;
use app\common\service\draw\engine\DrawSd;
use app\common\service\draw\QueueService;
use app\common\service\FileService;
use app\common\service\storage\Driver as StorageDriver;
use app\common\service\WordsService;
use Exception;
use think\facade\Log;

/**
 * 绘图逻辑
 * Class DrawLogic
 * @package app\api\logic
 */
class DrawLogic extends BaseLogic
{
    protected string $modelMainId;  // 主模型ID

    protected mixed  $user        = null;     // 用户信息
    protected int    $userId      = 0;        // 用户ID
    protected int    $type        = 1;        // 对话类型: [1=简单对话, 2=创作对话,3=角色]
    protected string $prompt      = '';       // 提问问题
    protected array  $usage       = [];       // tokens消费信息
    protected bool   $isVipFree   = false;    // 是否vip免费: false=不免费
    protected int|float $power    = 0;        // 每次绘画消耗的电量值
    protected array  $draw_config = [];
    protected string $drawApi     = '';       // 绘画渠道


    /**
     * @param int $userId
     */
    public function __construct(int $userId, $params = [])
    {
        $this->drawApi = $params['draw_api'] ?? DrawEnum::API_SD;
        // 绘图配置
        $this->draw_config = ConfigService::get('draw_config', $this->drawApi, DrawEnum::getDrawDefaultConfig($this->drawApi));
        // 基础参数
        $this->userId = $userId;
        // 消耗电量数
        $this->power = $this->draw_config['power'] ?? 0;

        // 获取用户
        $this->user = (new User())->where(['id' => $userId])->findOrEmpty();

        // 查询主要模型和lora是否被删除
        // ...
    }

    /**
     * @notes 创建绘画任务
     * @param $userId
     * @param $params
     * @return bool|array
     * @throws \Exception
     * @author JXDN
     * @date 2024/06/03 17:45
     */
    public function draw($userId, $params): bool|array
    {
        try {
            $params['prompt'] = trim($params['prompt'], ',');
            // 用户验证
            $this->checkUser();

            // 敏感词校验
            WordsService::sensitive($params['prompt']);
            // 内容审核-提示词
            WordsService::drawCensor($params['prompt']);

            // 写入绘画记录，扣除用户余额 (内含事务)
            $recordData = $this->drawRecordHandle($userId, $params);
            // 发起绘图请求
            $this->drawImagineHandle($recordData['record']);

            return ['records_id' => $recordData['record']['id']];
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 验证是否剩余vip次数
     * @param int $type
     * @return bool
     * @author JXDN
     * @date 2024/06/06 18:47
     */
    public function checkVip(int $type = MemberPackageEnum::APPLY_DRAW): bool
    {
        // is_limit=false (无限制次数), surplus_num=剩余次数
        $vips = UserMemberLogic::getUserPackageApply($this->userId, $type);
        foreach ($vips as $item) {
            if (!empty($item['channel']) && $item['channel'] == $this->drawApi) {
                if (!$item['is_limit'] || $item['surplus_num']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @notes 用户验证
     * @throws \Exception
     * @author fzr
     */
    private function checkUser(): void
    {
        if ($this->draw_config['status'] != 1) {
            throw new Exception('绘画功能已关闭');
        }

        if ($this->user->isEmpty()) {
            throw new Exception('非法会员');
        }

        if (YesNoEnum::YES == $this->user->is_blacklist) {
            throw new Exception('账号异常，请联系客服详询原因');
        }

        $this->isVipFree = $this->checkVip();

        if ($this->isVipFree) {
            $this->power = 0;
        } else {
            if ($this->power && $this->user->balance <= 0) {
                throw new Exception('对话余额不足', 1100);
            }
        }
    }

    /**
     * @notes 绘画记录处理
     * @param $userId
     * @param $params
     * @return array|false
     * @throws \Exception
     * @author 段誉
     * @date 2023/6/25 16:10\
     */
    public function drawRecordHandle($userId, $params): bool|array
    {
        $DrawRecords = new DrawRecords();
        $DrawRecords->startTrans();
        try {
            // 绘画类型
            $drawType = DrawEnum::TYPE_TEXT_TO_IMAGE;
            // 绘画动作
            $action = $params['action'];
            // 操作图片id
            $imageId = '';
            // 绘画版本
            $version = $params['version'] ?? '';
            // 绘画风格
            $style = 'default';
            // 绘画引擎
            $engine = 'sd';
            // 质量
            $quality = '';
            // 忽略参数
            $negativePrompt = $params['negative_prompt'] ?? '';
            // 垫图地址
            $imageBase = '';
            // mj重绘区域base64
            $imageMask = '';

            // 关键词
            $prompt = $params['prompt'];
            if (!empty($params['prompt_en'])) {
                $prompt = $params['prompt_en'];
            }

            // uv操作
            if ($action != DrawEnum::ACTION_GENERATE) {
                if (str_contains($action, DrawEnum::ACTION_UPSCALE)) {
                    $drawType = DrawEnum::TYPE_UPSCALE_IMAGE;
                }

                if (str_contains($action, DrawEnum::ACTION_VARIATION)) {
                    $drawType = DrawEnum::TYPE_VARIATION_IMAGE;
                }

                $imageId = $params['image_id'] ?? '';
            }

            // 垫图
            if (!empty($params['image_mask'])) {
                $drawType = DrawEnum::TYPE_IMAGE_TO_IMAGE;
                if ($action == DrawTaskEnum::ACTION_INPAINT) {
                    $imageMask = $params['image_mask'];
                } else {
                    $imageBase = $params['image_mask'];
                    if ($this->drawApi == DrawEnum::API_MJ_GOAPI) {
                        $prompt = $params['image_mask'] . ' ' . $prompt;
                    }
                }
            }

            // 本地sd
            if ($params['draw_api'] == DrawEnum::API_SD) {
                $engine = !empty($params['draw_model']) ? $params['draw_model'] : '';
            }

            // dalle绘画
            if ($params['draw_api'] == DrawEnum::API_DALLE3) {
                // 绘画引擎
                $engine = DrawEnum::API_DALLE3;
                // 风格
                $style = !empty($params['style']) ? $params['style'] : 'vivid';
                // 质量
                $quality = !empty($params['quality']) ? $params['quality'] : 'standard';
            }

            // mj绘画
            if ($params['draw_api'] == DrawEnum::API_MJ_GOAPI) {
                $engine = 'mj';
                // 图片比例
                if (!empty($params['size'])) {
                    $prompt .= ' --ar ' . $params['size'];
                }

                // 排除内容
                if (!empty($negativePrompt)) {
                    $prompt .= ' --no ' . $negativePrompt;
                }

                // 版本参数
                if (!empty($version)) {
                    if ($params['draw_model'] == DrawTaskEnum::MODEL_NIJI) {
                        $prompt .= ' --niji ' . $version;
                        if (!empty($params['style']) && $params['style'] != 'default') {
                            $style = $params['style'];
                            $prompt .= ' --style ' . $params['style'];
                        }
                    } else {
                        $prompt .= ' --v ' . $version;
                    }
                }

                // 高级参数
                if (!empty($params['complex_params']) && is_array($params['complex_params'])) {
                    $complexParams = '';
                    foreach ($params['complex_params'] as $paramsKey => $paramsItem) {
                        if (empty($paramsItem)) {
                            continue;
                        }
                        $complexParams .= ' --'.$paramsKey . ' ' . $paramsItem;
                    }
                    if (!empty($complexParams)) {
                        $prompt .= ' ' . $complexParams;
                    }
                }
            }

            // 增加绘图记录
            $record = $DrawRecords->create([
                'user_id'         => $userId,
                'type'            => $drawType,
                'action'          => $action,
                'prompt'          => $params['prompt'],
                'prompt_en'       => $params['prompt_en'] ?? '',
                'prompt_desc'     => $prompt,
                'prompt_other'    => $params['other'] ?? '',
                'scale'           => $params['size'] ?? '',
                'image_base'      => $imageBase,
                'image_mask'      => $imageMask,
                'status'          => DrawEnum::STATUS_NOT,
                'use_tokens'      => $this->power,
                'model'           => $params['draw_api'],
                'version'         => $version,
                'negative_prompt' => $params['negative_prompt'] ?? '',
                'style'           => $style,
                'engine'          => $engine,
                'quality'         => $quality,
                'ip'              => request()->ip(),
                'loras'           => json_encode($params['draw_loras'] ?? []) ?? '',
                'complex_params'  => json_encode($params['complex_params'] ?? []) ?? '',
                'origin_task_id'  => $params['origin_task_id'] ?? '',
            ])->toArray();

            // 如果是VIP则不是要扣费, 则扣除账户的费用
            $this->handleCost(AccountLogEnum::DEC);

            $DrawRecords->commit();

            return [
                'record'   => $record,
                'image_id' => $imageId,
            ];

        } catch (\Exception $e) {
            $DrawRecords->rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 绘画生成处理
     * @param $record
     * @throws \Exception
     * @author 段誉
     * @date 2023/7/25 16:57
     */
    public function drawImagineHandle($record): void
    {
        $recordsModel = new DrawRecords();
        try {
            // 更新状态为执行中
            $recordsModel->where(['id' => $record['id']])->update([
                'status'      => DrawEnum::STATUS_IN_PROGRESS,
                'update_time' => time(),
            ]);

            $pushData = [
                'record_id'   => $record['id'],
                'file_domain' => FileService::getFileDomain(),
            ];

            // 提交队列
            match ($record['model']) {
                DrawEnum::API_SD => QueueService::pushSd($pushData),
                DrawEnum::API_DALLE3 => QueueService::pushDalle($pushData),
                DrawEnum::API_MJ_GOAPI => $this->pushMj($pushData),
                default => throw new Exception('绘画模型参数异常'),
            };

        } catch (\Exception $e) {
            // 生成图片失败更新记录状态
            $recordsModel->where(['id' => $record['id']])->update([
                'status'      => DrawEnum::STATUS_FAIL,
                'notify_snap' => [],
                'fail_reason' => $e->getMessage(),
                'update_time' => time(),
            ]);

            // 生成失败, 没有任务id,回退用户电量值
            $this->handleCost(AccountLogEnum::INC);

            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 绘画费用处理
     * @param int $type
     * @return void
     * @throws \Exception
     * @author JXDN
     * @date 2024/06/04 15:19
     */
    public function handleCost(int $type): void
    {
        // 判断用户是否需要扣费
        $needsDeduction = (ChatRecordEnum::REPLYTYPE_MODEL == !$this->isVipFree);

        if ($needsDeduction) {
            // 根据类型决定是增加还是减少账户费用
            $changePrice = ($type === AccountLogEnum::INC)
                ? $this->user->balance + $this->power
                : $this->user->balance - $this->power;

            // 更新用户余额，确保余额不为负数
            $this->user->balance = max($changePrice, 0);
            $this->user->save();

            // 记录钱包变动日志
            $changeAction = ($type === AccountLogEnum::INC) ? AccountLogEnum::INC : AccountLogEnum::DEC;
            $changeType = ($type === AccountLogEnum::INC) ? AccountLogEnum::UM_INC_DRAW_FAIL : AccountLogEnum::UM_DEC_DRAW;
            try {
                UserAccountLog::add($this->userId, $changeType, $changeAction, $this->power, '', '应用绘画消耗余额', [], 0, []);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * @notes 失败记录处理
     * @param $record
     * @param array $otherUpdateData
     * @throws \Exception
     * @author mjf
     * @date 2024/1/4 16:10
     */
    public function failRecordHandle($record, array $otherUpdateData = []): void
    {
        $drawRecords = new DrawRecords();
        $updateData = [
            'status'      => DrawEnum::STATUS_FAIL,
            'update_time' => time(),
        ];
        $updateData = array_merge($updateData, $otherUpdateData);
        $drawRecords->where('id', $record['id'])->update($updateData);

        if(!$this->checkVip()) {
            $this->handleCost(AccountLogEnum::INC);
        }
    }

    /**
     * @notes 本地sd处理
     * @param $task
     * @param $response
     * @return void
     * @throws \Exception
     * @author JXDN
     * @date 2024/06/04 15:33
     */
    public function notifySd($task, $response): void
    {
        try {
            if (empty($response['images'][0])) {
                throw new \Exception('绘画返回结果异常，请检查SD服务器');
            }

            $base64 = base64_decode($response['images'][0]);
            $fileName = md5($base64) . '.png';
            // 下载原图，下载缩略图 考虑oss
            $baseDir = app()->getRootPath() . 'public/';
            $saveDir = 'uploads/draw/' . date('Ymd') . '/';
            $filePath = $saveDir . $fileName;

            if (!file_exists($baseDir . $saveDir)) {
                mkdir($baseDir . $saveDir, 0755, true);
            }
            // 保存图片文件到本地
            $localPath = $baseDir . $saveDir . $fileName;
            file_put_contents($localPath, $base64);

            // 非本地储存时上传图片到第三方
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage')
            ];
            if ($config['default'] != 'local') {
                // 第三方存储
                $StorageDriver = new StorageDriver($config);
                if (!$StorageDriver->fetch($localPath, $filePath)) {
                    throw new \Exception('SD绘图保存失败:' . $StorageDriver->getError());
                }
            }

            // 缩略图
            $thumbnail = $this->getThumbnail($localPath);

            if ($config['default'] != 'local') {
                unlink($localPath);
            }

            // 更新记录
            DrawRecords::update([
                'id'        => $task['id'],
                'status'    => DrawEnum::STATUS_SUCCESS,
                'image'     => $filePath,
                'image_url' => $filePath,
                'thumbnail' => $thumbnail,
            ]);

            // 图片审核
            $this->imageCensor($task['id'], $task['file_domain'] ?? '');

        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 下载图片
     * @param $downloadUrl
     * @param string $saveDir
     * @return string
     * @author JXDN
     * @date 2024/05/29 10:17
     */
    public function downloadImage($downloadUrl, string $saveDir = 'uploads/draw/'): string
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
            $fileName = md5($downloadUrl) . '.png';

            // 第三方存储
            if ($config['default'] == 'local') {
                $downSaveDir = app()->getRootPath() . 'public/' . $saveDir;
                $downRes = $this->downloadHandle($downloadUrl, $downSaveDir . date('Ymd') . '/', $fileName);
                if (empty($downRes)) {
                    throw new \Exception('图片下载失败,图片地址-' . $downloadUrl);
                }
                return $saveDir . date('Ymd') . '/' . $fileName;
            } else {
                $localPath = $saveDir . date('Ymd') . '/' . $fileName;
                $StorageDriver = new StorageDriver($config);
                if (!$StorageDriver->fetch($downloadUrl, $localPath)) {
                    throw new \Exception('绘图保存失败:' . $StorageDriver->getError());
                }
                return $localPath;
            }

        } catch (\Exception $e) {
            Log::write('绘图回调下载:' . $e->getMessage() . $e->getLine());
            return "";
        }
    }

    /**
     * @notes 生成缩略图
     * @param $originalImagePath
     * @return string
     * @author 段誉
     * @date 2023/8/4 17:00
     */
    public function getThumbnail($originalImagePath): string
    {
        try {
            // 保存路径
            $saveDir = 'uploads/thumbnail/' . date('Ymd') . '/';
            $fileName = self::imageUrlTrim($originalImagePath);

            // 缩略图保存路径
            $thumbnailImagePath = app()->getRootPath() . 'public/' . $saveDir;

            if (!is_dir($thumbnailImagePath)) {
                mkdir($thumbnailImagePath, 0755, true);
            }

            // 缩略图的宽度和高度
            $thumbnailWidth = 350;

            // 创建原始图像资源
            $info = getimagesize($originalImagePath);

            $fn = $info['mime'];// 获得图片类型；
            $originalImage = match ($fn) {
                'image/jpeg' => imagecreatefromjpeg($originalImagePath),
                'image/png' => imagecreatefrompng($originalImagePath),
                'image/webp' => imagecreatefromwebp($originalImagePath),
            };

            // 获取原始图像的宽度和高度
            $originalWidth = imagesx($originalImage);
            $originalHeight = imagesy($originalImage);

            // 计算缩略图的宽度和高度
            $thumbnailHeight = intval($originalHeight * $thumbnailWidth / $originalWidth);

            // 创建缩略图资源
            $thumbnailImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

            // 将原始图像复制到缩略图中，并进行缩放
            imagecopyresampled($thumbnailImage, $originalImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $originalWidth, $originalHeight);

            // 保存缩略图到文件
            imagepng($thumbnailImage, $thumbnailImagePath . $fileName);

            // 释放资源
            imagedestroy($originalImage);
            imagedestroy($thumbnailImage);

            // 第三方存储的情况
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage')
            ];

            if ($config['default'] != 'local') {
                // 第三方存储
                $filePath = $saveDir . $fileName;
                $localPath = $thumbnailImagePath . $fileName;
                $StorageDriver = new StorageDriver($config);
                if (!$StorageDriver->fetch($localPath, $filePath)) {
                    throw new \Exception('绘图缩略图保存失败:' . $StorageDriver->getError());
                }
            }

            return $saveDir . $fileName;
        } catch (\Exception $e) {
            Log::write('缩略图生成失败:' . $e->getMessage() . $e->getLine());
            return "";
        }
    }

    /**
     * @notes 图片地址处理
     * @param $image
     * @return string
     * @author mjf
     * @date 2023/9/26 15:56
     */
    public function imageUrlTrim($image): string
    {
        if (str_contains($image, '?')) {
            $image = strtok($image, '?');
        }
        return basename($image);
    }

    /**
     * @notes sd模型
     * @param $params
     * @return array
     * @author JXDN
     * @date 2024/05/28 15:31
     */
    public function getSdModel($params): array
    {
        try {
            $query = ['status' => 1];
            $params['category_id'] && $query['category_id'] = $params['category_id'];
            return DrawModel::where($query)
                ->order(['sort' => 'desc', 'id' => 'desc'])
                ->withoutField('update_time,delete_time')
                ->with(['loras' => function ($query) {
                    $query->getQuery()
                        ->hidden(['delete_time', 'update_time', 'pivot'])
                        ->withAttr(['cover' => function ($value) {
                        return FileService::getFileUrl($value);
                    }]);
                }])
                ->withAttr(['cover' => function ($value) {
                    return FileService::getFileUrl($value);
                }])
                ->select()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @notes sd模型分类
     * @return array
     * @author JXDN
     * @date 2024/05/28 15:31
     */
    public function getSdModelCategory(): array
    {
        try {
            return DrawModelCategory::where(['status' => 1])
                ->order(['sort' => 'desc', 'id' => 'desc'])
                ->withoutField('update_time,delete_time')
                ->select()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @notes sd采样算法
     * @return array
     * @author JXDN
     * @date 2024/05/28 15:31
     */
    public function getSdSamplers(): array
    {
        try {
            $apiConfig = ConfigService::get('draw_config', DrawEnum::API_SD, []);
            if (empty($apiConfig['proxy_url'])) {
                return [];
            } else {
                $engine = new DrawSd($apiConfig['proxy_url']);
                return $engine->getSamplers();
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @notes 图片审核
     * @param int $recordId
     * @param string $fileUrl
     * @author mjf
     * @date 2024/8/1 16:33
     */
    public function imageCensor(int $recordId, string $fileUrl = ''): void
    {
        $imageOpen = ConfigService::get('content_censor', 'image_open', 0);
        if (!$imageOpen) {
            return;
        }

        $drawRecord = (new DrawRecords)->findOrEmpty($recordId);
        if ($drawRecord->isEmpty() || empty($drawRecord['image'])) {
            return;
        }

        // 图片
        $image = FileService::getFileUrl($drawRecord['image']);
        if (!empty($fileUrl)) {
            $image = FileService::format($fileUrl, FileService::setFileUrl($drawRecord['image']));
        }

        // 错误信息
        $failReason = '';

        try {
            WordsService::drawCensor($image, DrawRecordEnum::TYPE_IMAGE);
            // 审核状态
            $censorStatus = DrawRecordEnum::CENSOR_STATUS_COMPLIANCE;
        } catch (\Exception $e) {
            $censorStatus = DrawRecordEnum::CENSOR_STATUS_FAIL;
            if (!empty($e->getCode()) && $e->getCode() == 10007) {
                $censorStatus = DrawRecordEnum::CENSOR_STATUS_NON_COMPLIANCE;
            }
            $failReason = $e->getMessage();
        }

        // 更新状态
        DrawRecords::where(['id' => $recordId])->update([
            'fail_reason'   => $failReason,
            'censor_status' => $censorStatus,
        ]);
    }

    /**
     * @notes
     * @param $task
     * @param $response
     * @throws \Exception
     * @author mjf
     * @date 2024/8/5 11:33
     */
    public function notifyDalle3($task, $response)
    {
        $base64Json = $response['data'][0]['b64_json'];
        if (empty($base64Json)) {
            throw new \Exception('响应异常');
        }

        $pattern  = '#^data:image/\w+;base64,#i';
        $base64   = base64_decode(preg_replace($pattern, '', $base64Json));
        $fileName = md5($base64) . '.png';
        // 下载原图
        $baseDir  = app()->getRootPath() . 'public/';
        $saveDir  = 'uploads/draw/' . date('Ymd') . '/';
        $filePath = $saveDir . $fileName;

        if (!file_exists($baseDir . $saveDir)) {
            mkdir($baseDir . $saveDir, 0755, true);
        }
        // 保存图片文件到本地
        $localPath = $baseDir . $saveDir . $fileName;
        file_put_contents($localPath, $base64);

        // 非本地储存时上传图片到第三方
        $config = [
            'default' => ConfigService::get('storage', 'default', 'local'),
            'engine'  => ConfigService::get('storage')
        ];
        if ($config['default'] != 'local') {
            // 第三方存储
            $StorageDriver = new StorageDriver($config);
            if (!$StorageDriver->fetch($localPath, $filePath)) {
                throw new \Exception('Dalle3绘图保存失败:' . $StorageDriver->getError());
            }
        }

        // 缩略图
        $thumbnail = $this->getThumbnail($localPath);

        if ($config['default'] != 'local') {
            unlink($localPath);
        }

        // 更新记录
        DrawRecords::update([
            'id'        => $task['id'],
            'status'    => DrawEnum::STATUS_SUCCESS,
            'image'     => $filePath,
            'image_url' => $filePath,
            'thumbnail' => $thumbnail,
        ]);

        // 图片审核
        $this->imageCensor($task['id'], $task['file_domain'] ?? '');
    }

    /**
     * @notes mj回调
     * @param $response
     * @return bool|string
     * @author mjf
     * @date 2024/8/15 15:45
     */
    public function notifyMj($response): bool|string
    {
        ignore_user_abort(true);
        set_time_limit(300);
        try {
            if (empty($response['status']) || empty($response['task_id'])) {
                throw new Exception('响应信息异常');
            }

            // 绘图记录
            $recordModel = new DrawRecords();
            $record = $recordModel->where(['task_id' => $response['task_id']])->findOrEmpty();
            if ($record->isEmpty()) {
                throw new Exception("绘图记录信息不存在");
            }

            // 已标记成功或失败的记录不处理
            if (in_array($record['status'], [DrawEnum::STATUS_FAIL, DrawEnum::STATUS_SUCCESS])) {
                Log::write("绘图记录状态为已成功或失败，无需处理--" . json_encode($response, JSON_UNESCAPED_UNICODE));
                return false;
            }

            // 失败
            if ($response['status'] == 'failed') {
                // 更新信息
                $errorMsg = $response['task_result']['error_messages'][0] ?? '';
                $recordModel->where(['id' => $record['id']])->update([
                    'status'      => DrawEnum::STATUS_FAIL,
                    'fail_reason' => $errorMsg,
                    'update_time' => time(),
                    'notify_snap' => json_encode($response, JSON_UNESCAPED_UNICODE),
                ]);

                $this->failRecordHandle($record, $errorMsg);
                return false;
            }

            // 成功
            if ($response['status'] == 'finished') {
                $imageUrl  = $response['task_result']['image_url'] ?? '';

                $recordModel->where(['id' => $record['id']])->update([
                    'status'       => DrawEnum::STATUS_SUCCESS,
                    'image'        => $imageUrl,
                    'thumbnail'    => $imageUrl,
                    'image_url'    => $imageUrl,
                    'image_id'     => $response['task_result']['image_id'] ?? '',
                    'able_actions' => json_encode($response['task_result']['actions'] ?? []),
                    'notify_snap'  => json_encode($response, JSON_UNESCAPED_UNICODE),
                    'update_time'  => time(),
                ]);

                $image     = $this->downloadImage($imageUrl);
                $thumbnail = $this->getThumbnail($imageUrl);

                if (!empty($image)) {
                    $recordModel->where(['id' => $record['id']])->update([
                        'image'     => $image,
                        'thumbnail' => $thumbnail,
                    ]);
                    self::imageCensor($record['id']);
                }

                return true;
            }
            return false;
        } catch (Exception $e) {
            $errorMsg = !empty($response)
                ? $e->getMessage() . '--请求内容--' . json_encode($response, JSON_UNESCAPED_UNICODE)
                : $e->getMessage() . $e->getLine();
            Log::write('MJ绘图回调失败:' . $errorMsg);
            return $e->getMessage();
        }
    }

    /**
     * @notes 收藏
     * @param $userId
     * @param $params
     * @author 段誉
     * @date 2023/6/27 11:33
     */
    public static function collect($userId, $params)
    {
        if ($params['status']) {
            $collect = DrawRecordsCollect::where([
                'user_id' => $userId,
                'square_id' => $params['records_id'],
            ])->findOrEmpty();
            if($collect->isEmpty()) {
                // 收藏
                DrawRecordsCollect::create([
                    'user_id' => $userId,
                    'square_id' => $params['records_id'],
                ]);
            }
        } else {
            // 取消收藏
            DrawRecordsCollect::where([
                'user_id' => $userId,
                'square_id' => $params['records_id'],
            ])->delete();
        }
    }


    /**
     * @notes 绘画详情
     * @param $id
     * @param $userId
     * @return DrawRecords|array|mixed|string|\think\Model
     * @author cjhao
     * @date 2024/7/25 17:38
     */
    public static function detail($id,$userId)
    {
        if(empty($id)){
            return '请选择绘画记录';
        }
        $draw = DrawRecords::where(['id'=>$id])
            ->withoutField('notify_snap,status,censor_status,update_time,delete_time')
            ->findOrEmpty();
        if($draw->isEmpty()){
            return '绘画记录不存在';
        }
        $draw->nickname = User::where(['id'=>$draw['user_id']])->value('nickname');
        $recordsId = DrawRecordsCollect::alias('dr')
            ->join('draw_square ds','dr.square_id = ds.id')
            ->where(['user_id'=>$userId,'ds.records_id'=>$id])
            ->value('ds.records_id');
        $draw->is_collect = $recordsId  ? 1 : 0;
        return $draw->toArray();
    }

    /**
     * @notes mj请求
     * @param $data
     * @throws \Exception
     * @author mjf
     * @date 2024/8/15 13:59
     */
    public function pushMj($data): void
    {
        $recordModel = new DrawRecords();
        $record      = $recordModel->where('id', $data['record_id'])->findOrEmpty();

        $drawDriver = new DrawDriver(DrawEnum::API_MJ_GOAPI);
        $actionData = DrawEnum::getActionAndIndex($record['action']);

        $response = $drawDriver->imagine([
            'prompt'         => $record['prompt_desc'],
            'origin_task_id' => $record['origin_task_id'],
            'action'         => $actionData['action'],
            'index'          => $actionData['index'],
            'scale'          => $record['scale'],
            'mask'           => $record['image_mask'],
        ]);

        if ($response['status'] != 'success') {
            $errorMsg = !empty($response['message']) ? $response['message'] : '提交失败';
            throw new Exception($errorMsg);
        }

        $recordModel->where(['id' => $record['id']])->update([
            'task_id'     => $response['task_id'] ?? '',
            'notify_snap' => json_encode($response, JSON_UNESCAPED_UNICODE),
            'status'      => DrawEnum::STATUS_IN_PROGRESS,
            'update_time' => time(),
        ]);
    }

    /**
     * @notes 下载处理
     * @param $url
     * @param $saveDir
     * @param $fileName
     * @return string
     * @author mjf
     * @date 2024/8/20 16:16
     */
    public function downloadHandle($url, $saveDir, $fileName): string
    {
        if (!file_exists($saveDir)) {
            mkdir($saveDir, 0775, true);
        }

        $fileSrc = $saveDir . $fileName;
        file_exists($fileSrc) && unlink($fileSrc);

        $fileContent = file_get_contents($url);
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