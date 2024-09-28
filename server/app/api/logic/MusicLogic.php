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

use app\api\service\MusicService;
use app\common\enum\ChatEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\enum\MusicEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\model\music\MusicRecord;
use app\common\model\music\MusicRecordsCollect;
use app\common\model\music\MusicSquare;
use app\common\model\music\MusicStyle;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\service\ai\chat\AzureService;
use app\common\service\ai\chat\BaiduService;
use app\common\service\ai\chat\DoubaoService;
use app\common\service\ai\chat\OllamaService;
use app\common\service\ai\chat\OpenaiService;
use app\common\service\ai\chat\QwenService;
use app\common\service\ai\chat\SystemService;
use app\common\service\ai\chat\XunfeiService;
use app\common\service\ai\chat\ZhipuService;
use app\common\service\ConfigService;
use app\common\service\FileService;
use Exception;

/**
 * 音乐逻辑
 */
class MusicLogic extends BaseLogic
{
    /**
     * @notes 生成音乐
     * @param int $userId
     * @param array $params
     * @return bool|array
     * @author mjf
     * @date 2024/5/29 16:23
     */
    public static function generate(int $userId, array $params): bool|array
    {
        try {
            $musicService = new MusicService($params, $userId);
            $musicService->music();
            $recordIds = $musicService->getTaskRecordIds();
            return ['record_id' => $recordIds];
        } catch (Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 歌词联想
     * @param int $userId
     * @param array $params
     * @return array|bool
     * @author mjf
     * @date 2024/5/29 19:03
     */
    public static function imagine(int $userId, array $params): array|bool
    {
        try {
            $prompt = $params['prompt'] ?? '';
            // 读取配置
            $imagineStatus = ConfigService::get('music_imagine', 'status', 0);
            if (!$imagineStatus) {
                throw new Exception('该功能已关闭');
            }

            $user = (new User())->findOrEmpty($userId);

            $imaginePrice = ConfigService::get('music_imagine', 'price', 0);
            if ($imaginePrice > 0) {
                if ($user['balance'] < $imaginePrice) {
                    throw new Exception('余额不足');
                }
            }

            $imagineModelCostId = ConfigService::get('music_imagine', 'cost_id', 0);

            // 查询小模型
            $modelModelsCost = new ModelsCost();
            $subModels = $modelModelsCost->where(['type' => ChatEnum::MODEL_TYPE_CHAT])
                ->where(['id' => $imagineModelCostId])
                ->findOrEmpty()->toArray();

            if (!$subModels || !$subModels['status']) {
                $error = !$subModels ? '对话模型可能已被下架了' : '对话模型已被下架了';
                throw new Exception($error);
            }

            // 查询大模型
            $bigModel = (new Models())->where(['id' => $subModels['model_id']])->findOrEmpty()->toArray();
            if (!$bigModel || !$bigModel['is_enable']) {
                $error = !$bigModel ? '对话模型已被下架!' : '对模型已被下架了!';
                throw new Exception($error);
            }

            $channel = $subModels['channel'];
            $configs = json_decode($bigModel['configs'], true);
            $configs['channel'] = $channel;
            $configs['model'] = $subModels['name'];
            $configs['model_id'] = $subModels['model_id'];

            $chatService = match ($channel) {
                'openai' => (new OpenaiService($configs)),
                'xunfei' => (new XunFeiService($configs)),
                'zhipu'  => (new ZhipuService($configs)),
                'baidu'  => (new BaiduService($configs)),
                'qwen'   => (new QwenService($configs)),
                'azure'  => (new AzureService($configs)),
                'doubao' => (new DoubaoService($configs)),
                'ollama' => (new OllamaService($configs)),
                'system' => (new SystemService($configs)),
                default  => throw new Exception('模型配置错误了'. $channel)
            };

            $messages[] = ['role'  => 'user','content' => '写一首关于' . $prompt . '的歌词,1200字以内'];
            $chatResult = $chatService->chatHttpRequest($messages);
            if (isset($chatResult['error'])) {
                $error = $chatResult['error']['message'] ?? '操作失败';
                throw new Exception($error);
            }

            $content = $chatResult['choices'][0]['message']['content'] ?? '';

            // 扣除消耗
            if ($imaginePrice > 0) {
                // 账户扣费
                $changePrice = $user['balance'] - $imaginePrice;
                $user->balance = max($changePrice, 0);
                $user->save();

                // 钱包变动
                $changeType   = AccountLogEnum::UM_DEC_MUSIC_IMAGINE;
                $changeAction = AccountLogEnum::DEC;
                UserAccountLog::add($userId, $changeType, $changeAction, $imaginePrice);
            }

            return ['content' => $content];

        } catch (Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 详情
     * @param $params
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2024/5/30 10:38
     */
    public static function detail($params, $userId): array
    {
        if (empty($params['id'])) {
            return [];
        }

        $model = new MusicRecord();
        $lists = $model->alias('MR')->field(['MR.id','MR.title','MR.prompt','MR.tags','MR.image_url','MR.image_large_url',
            'MR.video_url', 'MR.audio_url', 'MR.lyric', 'MR.custom_mode', 'MR.make_instrumental','MR.duration',
            'MR.style_id','MR.status', 'MR.create_time','U.nickname,U.avatar'])
//            ->where(['user_id' => $userId])
            ->whereIn('MR.id', $params['id'])
            ->join('user U','MR.user_id = U.id')
            ->order('MR.id desc')
            ->append(['style_desc'])
            ->select()
            ->toArray();
        $recordsId = MusicRecordsCollect::alias('mr')
            ->where(['mr.user_id'=>$userId])
            ->join('music_square ms','mr.square_id = ms.id')
            ->column('ms.records_id') ?: [];
        foreach ($lists as $key => $list){
            $lists[$key]['is_collect'] = 0;
            if(in_array($list['id'],$recordsId)){
                $lists[$key]['is_collect'] = 1;
            }
            $lists[$key]['avatar'] = FileService::getFileUrl($list['avatar']);
        }

        return $lists;
    }

    /**
     * @notes 配置
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2024/5/30 17:09
     */
    public static function config($userId): array
    {
        $styleModel = new MusicStyle();
        $style = $styleModel
            ->field(['id,name,image'])
            ->where(['status' => YesNoEnum::YES])
            ->order(['sort'=>'desc','id'=>'desc'])
            ->select()
            ->toArray();

        $imagineStatus = ConfigService::get('music_imagine', 'status', 0);
        $imaginePrice = ConfigService::get('music_imagine', 'price', 0);

        $musicService = new MusicService([], $userId);
        $musicModels = $musicService->getMusicConfig();

        if (!empty($musicModels['models']) && is_array($musicModels['models'])) {
            foreach ($musicModels['models'] as &$itemModel) {
                if (!empty($itemModel['version']) && is_array($itemModel['version'])) {
                    $versionData = [];
                    foreach ($itemModel['version'] as $itemVersion) {
                        $versionData[$itemVersion] = MusicEnum::getVersion($itemVersion);
                    }
                    $itemModel['version'] = $versionData;
                }
            }
        }

        return [
            'is_member' => $musicService->checkVip(MemberPackageEnum::APPLY_MUSIC),
            'channel'   => $musicModels['channel'],
            'model'     => $musicModels['models'],
            'style'     => $style,
            'imagine'   => [
                'status'    => $imagineStatus,
                'price'     => format_amount_zero($imaginePrice),
            ]
        ];
    }

    /**
     * @notes 删除
     * @param int|array $params
     * @return bool
     * @author mjf
     * @date 2024/5/27 12:13
     */
    public static function del(int|array $params, $userId): bool
    {
        return MusicRecord::destroy(['user_id' => $userId, 'id' => $params['id'] ?? 0]);
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
            $collect = MusicRecordsCollect::where([
                'user_id' => $userId,
                'square_id' => $params['records_id'],
            ])->findOrEmpty();
            if($collect->isEmpty()) {
                // 收藏
                MusicRecordsCollect::create([
                    'user_id' => $userId,
                    'square_id' => $params['records_id'],
                ]);
            }
        } else {
            // 取消收藏
            MusicRecordsCollect::where([
                'user_id' => $userId,
                'square_id' => $params['records_id'],
            ])->delete();
        }
    }


}