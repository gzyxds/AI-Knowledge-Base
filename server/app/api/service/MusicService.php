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

namespace app\api\service;

use app\common\enum\member\MemberPackageEnum;
use app\common\enum\MusicEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\UserMemberLogic;
use app\common\model\music\MusicRecord;
use app\common\model\music\MusicStyle;
use app\common\model\music\MusicTask;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\service\ConfigService;
use app\common\service\music\GoApiService;
use app\common\service\music\OpenaiHKService;
use app\common\service\WordsService;
use Exception;

/**
 * 音乐服务类
 */
class MusicService
{
    protected string $title;
    protected string $prompt;
    protected int $customMode;
    protected string $styleTags;
    protected string $styleId;
    protected string $styleCustom;
    protected string $mv;
    protected int $makeInstrumental;

    protected string $price;
    protected string $channel;
    protected array $musicConfig = [];

    protected int $taskId = 0;
    protected int $userId = 0;
    protected User|null $user = null;

    /**
     * @throws Exception
     */
    public function __construct(array $params = [], int $userId = 0)
    {
        // 渠道配置
        $channelConfigLists = $this->getMusicConfig();
        // 音乐渠道
        $this->channel = !empty($params['channel']) ? $params['channel'] : $channelConfigLists['channel'];
        // 渠道配置
        $this->musicConfig = $channelConfigLists['models'][$this->channel] ?? [];
        if (empty($this->musicConfig['status'])) {
            throw new Exception('配置异常');
        }

        $this->setParamsAttr($params, $userId);
    }

    /**
     * @notes 设置参数属性
     * @param array $params
     * @param int $userId
     * @author mjf
     * @date 2024/5/29 16:31
     */
    public function setParamsAttr(array $params = [], int $userId = 0)
    {
        $this->userId = $userId;
        $this->user = (new User())->where(['id' => $userId])->findOrEmpty();

        $this->title      = $params['title'] ?? '';
        $this->customMode = intval($params['custom_mode'] ?? 0);
        $this->prompt     = $params['prompt'] ?? '';
        $this->styleId    = implode(',', $params['style_id'] ?? []);
        $this->styleCustom = $params['style_custom'] ?? '';
        $this->styleTags  = '';
        $this->mv         = $params['version'] ?? MusicEnum::CHIRP_V30;
        $this->makeInstrumental = intval($params['make_instrumental'] ?? 0);

        // 获取价格
        $this->price = $this->musicConfig['price'] ?? 0;
        if ($this->checkVip(MemberPackageEnum::APPLY_MUSIC)) {
            $this->price = 0;
        }
    }

    /**
     * @notes  设置风格
     * @author mjf
     * @date 2024/5/29 15:57
     */
    public function setStyle()
    {
        if ($this->customMode) {
            $this->styleTags = $this->styleCustom;
            if (!empty($this->styleId)) {
                $model = new MusicStyle();
                $styleLists = $model->whereIn('id', $this->styleId)->select()->toArray();

                $styleArr = [];
                foreach ($styleLists as $style) {
                    $styleArr[] = !empty($style['value']) ? $style['value'] : $style['name'];
                }
                $this->styleTags = implode(',', $styleArr);
            }
        }
    }

    /**
     * @notes 任务id
     * @param int $taskId
     * @author mjf
     * @date 2024/5/29 17:26
     */
    public function setTaskId(int $taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @notes 提交音乐任务
     * @throws Exception
     * @author mjf
     * @date 2024/5/29 16:21
     */
    public function music()
    {
        // 用户信息校验
        $this->checkUser();
        $this->wordsAudit();
        // 风格设置
        $this->setStyle();

        $model = new User();
        $model->startTrans();
        try {
            // 扣除余额
            $this->decBalance();
            // 提交任务
            $this->submitTask();
            $model->commit();
        } catch (Exception $e) {
            $model->rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 提示词审核
     * @throws Exception
     * @author mjf
     * @date 2024/5/29 16:17
     */
    public function wordsAudit()
    {
        $checkContent = $this->title . $this->prompt;
        // 敏感词验证
        WordsService::sensitive($checkContent);
        // 问题审核(百度)
        WordsService::askCensor($checkContent);
    }

    /**
     * @notes 用户校验
     * @throws Exception
     * @author mjf
     * @date 2024/5/29 15:50
     */
    public function checkUser()
    {
        if ($this->user->isEmpty()) {
            throw new Exception('非法会员');
        }

        if ($this->price && $this->user->balance < $this->price) {
            throw new Exception('余额不足');
        }

        if (YesNoEnum::YES == $this->user->is_blacklist) {
            throw new Exception('您已被管理员禁止操作,请联系客服详询原因。');
        }
    }

    /**
     * @notes 扣除余额
     * @author mjf
     * @date 2024/5/29 15:44
     */
    public function decBalance()
    {
        if ($this->price <= 0) {
            return;
        }

        // 账户扣费
        $changePrice = $this->user['balance'] - $this->price;
        $this->user->balance = max($changePrice, 0);
        $this->user->save();

        // 钱包变动
        $changeType   = AccountLogEnum::UM_DEC_MUSIC;
        $changeAction = AccountLogEnum::DEC;
        UserAccountLog::add($this->userId, $changeType, $changeAction, $this->price);
    }

    /**
     * @notes
     * @throws Exception
     * @author mjf
     * @date 2024/5/29 16:09
     */
    public function submitTask()
    {
        // 提交任务
        $task = MusicTask::create([
            'user_id'           => $this->userId,
            'style_id'          => $this->styleId,
            'tags'              => $this->styleTags,
            'channel'           => $this->channel,
            'model'             => MusicEnum::SUNO,
            'title'             => $this->title,
            'prompt'            => $this->prompt,
            'custom_mode'       => $this->customMode,
            'make_instrumental' => $this->makeInstrumental,
            'tokens'            => $this->price,
            'ip'                => request()->ip(),
            'status'            => MusicEnum::STATUS_WAIT,
        ]);

        $this->setTaskId($task['id']);

        match ($this->channel) {
            MusicEnum::GOAPI => $this->submitGoApi($task),
            MusicEnum::OPENAIHK => $this->submitOpenaiHk($task),
            default => throw new Exception("系统错误"),
        };
    }

    /**
     * @notes 提交任务（GoApi渠道）
     * @param $task
     * @throws Exception
     * @author mjf
     * @date 2024/6/21 17:37
     */
    public function submitGoApi($task)
    {
        $musicService = new GoApiService();
        $response = $musicService->music([
            'title'             => $this->title,
            'prompt'            => $this->prompt,
            'tags'              => $this->styleTags,
            'custom_mode'       => $this->customMode,
            'make_instrumental' => $this->makeInstrumental,
            'mv'                => $this->mv,
        ]);

        if (empty($response['task_id'])) {
            throw new Exception($response['message'] ?? '音乐任务提交失败');
        }

        // 更新任务
        MusicTask::update([
            'task_id'   => $response['task_id'],
            'status'    => MusicEnum::STATUS_IN_PROGRESS,
        ], ['id' => $task['id']]);

        // 增加记录 (一个任务两个音频记录)
        $recordNum = 2;
        $recordsData = [];
        for ($i = 0; $i < $recordNum; $i++) {
            $recordsData[] = [
                'user_id'           => $this->userId,
                'task_id'           => $task['id'],
                'style_id'          => $task['style_id'],
                'tags'              => $task['tags'],
                'channel'           => $this->channel,
                'model'             => MusicEnum::SUNO,
                'title'             => $this->title,
                'prompt'            => $this->prompt,
                'custom_mode'       => $this->customMode,
                'make_instrumental' => $this->makeInstrumental,
                'tokens'            => $this->price,
                'status'            => MusicEnum::STATUS_IN_PROGRESS,
                'mv'                => $this->mv,
                'create_time'       => time(),
            ];
        }

        $recordModel = new MusicRecord();
        $recordModel->insertAll($recordsData, 2);
    }

    /**
     * @notes 提交任务（OpenAiHk渠道）
     * @param $task
     * @throws Exception
     * @author mjf
     * @date 2024/6/21 17:53
     */
    public function submitOpenaiHk($task)
    {
        $musicService = new OpenaiHKService();
        $response = $musicService->music([
            'title'             => $this->title,
            'prompt'            => $this->prompt,
            'tags'              => $this->styleTags,
            'custom_mode'       => $this->customMode,
            'make_instrumental' => $this->makeInstrumental,
            'mv'                => $this->mv,
        ]);

        if (empty($response['id'])) {
            throw new Exception($response['message'] ?? '音乐任务提交失败');
        }

        // 更新任务
        MusicTask::update([
            'task_id'   => $response['id'],
            'status'    => MusicEnum::STATUS_IN_PROGRESS,
        ], ['id' => $task['id']]);

        // 增加记录 (一个任务两个音频记录)
        $recordsData = [];
        foreach ($response['clips'] as $clip) {
            $recordsData[] = [
                'user_id'           => $this->userId,
                'task_id'           => $task['id'],
                'clips_id'          => $clip['id'],
                'style_id'          => $task['style_id'],
                'tags'              => $task['tags'],
                'channel'           => $this->channel,
                'model'             => MusicEnum::SUNO,
                'title'             => $this->title,
                'prompt'            => $this->prompt,
                'custom_mode'       => $this->customMode,
                'make_instrumental' => $this->makeInstrumental,
                'tokens'            => $this->price,
                'status'            => MusicEnum::STATUS_IN_PROGRESS,
                'mv'                => $this->mv,
                'ip'                => request()->ip(),
                'create_time'       => time(),
            ];
        }

        $recordModel = new MusicRecord();
        $recordModel->insertAll($recordsData, 2);
    }


    /**
     * @notes 任务关联记录id
     * @return array
     * @author mjf
     * @date 2024/5/29 16:09
     */
    public function getTaskRecordIds (): array
    {
        $recordModel = new MusicRecord();
        return $recordModel->where(['task_id' => $this->taskId])->column('id');
    }

    /**
     * @notes 配置
     * @return array
     * @author mjf
     * @date 2024/5/28 18:30
     */
    public function getMusicConfig(): array
    {
        $musicModels    = MusicEnum::getChannelDefaultConfig();
        $musicChannel   = ConfigService::get('music_model', 'channel', MusicEnum::GOAPI);
        $cacheConfigs   = ConfigService::get('music_model', 'configs', []);

        $config = [];
        foreach ($musicModels as $key => $item) {
            $cacheConfig = $cacheConfigs[$key] ?? [];
            $item = array_merge($item, $cacheConfig);
            unset($item['tips'], $item['website']);
            $item['status'] = $key == $musicChannel ? 1 : 0;
            $config[$key] = $item;
        }
        return [
            'channel'   => $musicChannel,
            'models'    => $config,
        ];
    }

    /**
     * @notes 会员
     * @param int $type
     * @return bool
     * @author mjf
     * @date 2024/6/4 11:28
     */
    public function checkVip(int $type): bool
    {
        // is_limit=false (无限制次数), surplus_num=剩余次数
        $vips = UserMemberLogic::getUserPackageApply($this->userId, $type);
        foreach ($vips as $item) {
            if (!$item['is_limit'] || $item['surplus_num']) {
                return true;
            }
        }
        return false;
    }


}