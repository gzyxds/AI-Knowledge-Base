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
namespace app\common\logic;

use app\adminapi\logic\member\MemberPackageLogic;
use app\common\enum\ChatEnum;
use app\common\enum\ChatRecordEnum;
use app\common\enum\draw\DrawEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\enum\MusicEnum;
use app\common\enum\VideoEnum;
use app\common\model\chat\ChatRecord;
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\model\draw\DrawRecords;
use app\common\model\kb\KbKnowQa;
use app\common\model\kb\KbRobotRecord;
use app\common\model\member\MemberPackage;
use app\common\model\member\UserMember;
use app\common\model\music\MusicTask;
use app\common\model\search\AiSearchRecord;
use app\common\model\video\VideoRecord;
use app\common\pgsql\KbEmbedding;
use app\common\service\ConfigService;

/**
 * 会员等级逻辑类
 * Class UserMemberLogic
 * @package app\common\logic
 */
class UserMemberLogic
{

    /**
     * @notes 返回用户最高等级
     * @param int $userId
     * @param int $isOverdue 如果会员查询出来是空，是否返回已过期的，false不返回，true返回
     * @return array
     * @author cjhao
     * @date 2024/5/30 9:47
     */
    public static function getUserMember(int $userId,$isOverdue = false)
    {
        // 从高到低获取套餐
        $packageIds = MemberPackage::order('sort desc,id desc')
            ->append(['apply_list'])
            ->column('id');
        $whereOr = [[
                        ['user_id', '=', $userId],
                        ['is_perpetual', '=', 0],
                        ['member_end_time', '>', time()],
                        ['package_id', 'in', $packageIds]
                    ], [
                        ['is_perpetual', '=', 1],
                        ['user_id', '=', $userId],
                        ['package_id', 'in', $packageIds]
                    ]];
        // 查询当前最高等级的会员
        $where = [];
        $orderRaw = '';
        if ($packageIds) {
            $orderRaw = 'FIELD(package_id,' . implode(',', $packageIds) . ')';
        } else {
            $orderRaw = 'id desc';
        }
        $userMember = UserMember::whereOr($whereOr)
            ->orderRaw($orderRaw)
            ->field('id,package_id,member_end_time,is_perpetual')
            ->findOrEmpty()
            ->toArray();
        $memberOverdue = false;
        if (empty($userMember)) {
            if(false === $isOverdue){
                return [];
            }
            $memberOverdue = true;
            $userMember = UserMember::where(['user_id'=>$userId,'package_id'=>$packageIds,'is_clear'=>0])
                ->orderRaw($orderRaw)
                ->field('id,package_id,member_end_time,is_perpetual')
                ->findOrEmpty()
                ->toArray();
            if(empty($userMember)){
                return [];
            }
        }
//        //如果不是永久会员，拿按下单时长获取会员时长
//        if(!$userMember['is_perpetual']){
//            $userMember = UserMember::where(['user_id'=>$userId,'package_id'=>$userMember['package_id']])
//                ->where('member_end_time','>',time())
//                ->field('id,package_id,member_end_time,is_perpetual')
//                ->order('id desc')
//                ->findOrEmpty()->toArray();
//        }
        // 查询套餐
        $where[] = ['id', 'in', $userMember['package_id']];
        $packageInfo = MemberPackage::where($where)
            ->append(['apply_list'])
            ->order('sort desc,id desc')
            ->findOrEmpty()->toArray();
        // 套餐信息
        $packageInfo['member_end_time'] = $userMember['member_end_time'];
        $packageInfo['is_perpetual'] = $userMember['is_perpetual'];
        $packageInfo['user_member_id'] = $userMember['id'];
        $packageInfo['is_overdue'] = $memberOverdue;
        return $packageInfo;
    }




    /**
     * @notes 获取会员套餐应用剩余次数
     * @param int $userId
     * @param int $type :1-对话；2-向量；3-绘画；4-音乐;5-思维导图;6-AI视频;7-AI搜索
     * @return array
     * @author cjhao
     * @date 2024/5/30 10:11
     */
    public static function getUserPackageApply(int $userId, int $type = MemberPackageEnum::APPLY_CHAT)
    {

        $chatLists = [];
        $vectorLists = [];
        $drawLists = [];
        $musicLists = [];
        $mindmapLists = [];
        $videoLists = [];
        $aisearchLists = [];
        $packageLists = self::getUserMember($userId);
        if (empty($packageLists)) {
            return [];
        }
        $applyLists = $packageLists['apply_list'];
        foreach ($applyLists as $apply) {
            switch ($apply['type']) {
                case MemberPackageEnum::APPLY_CHAT:
                    $chatLists[] = $apply;
                    break;
                case MemberPackageEnum::APPLY_VECTOR:
                    $vectorLists[] = $apply;
                    break;
                case MemberPackageEnum::APPLY_DRAW:
                    $drawLists[] = $apply;
                    break;
                case MemberPackageEnum::APPLY_MUSIC:
                    $musicLists[] = $apply;
                    break;
                case MemberPackageEnum::APPLY_MINDMAP:
                    $mindmapLists[] = $apply;
                    break;
                case MemberPackageEnum::APPLY_VIDEO:
                    $videoLists[] = $apply;
                    break;
                case MemberPackageEnum::APPLY_AISEARCH:
                    $aisearchLists[] = $apply;
                    break;
            }
        }
        //如果有新增的模型
        $defaultModels = self::getModels();
        $chatLists = array_column($chatLists,null,'channel');
        foreach ($defaultModels['chat_list'] as $defaultModel){
            if(!isset($chatLists[$defaultModel['channel']])){
                $chatLists[$defaultModel['channel']] =  $defaultModel;
            }
        }
        $vectorLists = array_column($vectorLists,null,'channel');
        foreach ($defaultModels['vector_list'] as $defaultModel){
            if(!isset($vectorLists[$defaultModel['channel']])){
                $vectorLists[$defaultModel['channel']] =  $defaultModel;
            }
        }
        $drawLists = array_column($drawLists,null,'channel');
        foreach ($defaultModels['draw_list'] as $defaultModel){
            if(!isset($drawLists[$defaultModel['channel']])){
                $drawLists[$defaultModel['channel']] =  $defaultModel;
            }
        }
        $musicLists = array_column($musicLists,null,'channel');
        foreach ($defaultModels['music_list'] as $defaultModel){
            if(!isset($musicLists[$defaultModel['channel']])){
                $musicLists[$defaultModel['channel']] =  $defaultModel;
            }
        }
        $mindmapLists = array_column($mindmapLists,null,'channel');
        foreach ($defaultModels['mindmap_list'] as $defaultModel){
            if(!isset($mindmapLists[$defaultModel['channel']])){
                $mindmapLists[$defaultModel['channel']] =  $defaultModel;
            }
        }
        $videoLists = array_column($videoLists,null,'channel');
        foreach ($defaultModels['video_list'] as $defaultModel){
            if(!isset($videoLists[$defaultModel['channel']])){
                $videoLists[$defaultModel['channel']] =  $defaultModel;
            }
        }
        $aisearchLists = array_column($aisearchLists,null,'channel');
        foreach ($defaultModels['aisearch_list'] as $defaultModel){
            if(!isset($aisearchLists[$defaultModel['channel']])){
                $aisearchLists[$defaultModel['channel']] =  $defaultModel;
            }
        }
        // 对话向量用量
        if (MemberPackageEnum::APPLY_CHAT == $type || MemberPackageEnum::APPLY_VECTOR == $type) {
            $channelLists = array_column($chatLists, 'channel');
            // 对话用量
            if (MemberPackageEnum::APPLY_CHAT == $type) {
                // 普通对话使用量
                $chatCount = ChatRecord::withTrashed()
                    ->where(['chat_model_id' => $channelLists, 'user_id' => $userId])
                    ->where('type','<>',ChatRecordEnum::CHAT_MINDMAP)
                    ->whereDay('create_time')
                    ->group('chat_model_id')
                    ->column('count(id) as count', 'chat_model_id');
                // 机器人对话使用量
                $robotCount = KbRobotRecord::withTrashed()
                    ->where(['chat_model_id' => $channelLists, 'user_id' => $userId])
                    ->whereDay('create_time')
                    ->group('chat_model_id')
                    ->column('count(id) as count', 'chat_model_id');
                // QA拆分使用量
                $qaCount = KbKnowQa::withTrashed()
                    ->where(['model_id' => $channelLists, 'user_id' => $userId])
                    ->where(['status'=>2])
                    ->whereDay('create_time')
                    ->group('model_id')
                    ->column('count(id) as count', 'model_id');
                // 对话用量
                foreach ($chatLists as $key => $value) {
                    $dayLimit = $value['day_limit'];
                    $status = $value['status'];
                    $useNum = ($chatCount[$value['channel']] ?? 0)+($robotCount[$value['channel']]??0) + ($qaCount[$value['channel']]??0);
                    $chatLists[$key]['use_num'] = $useNum;
                    $chatLists[$key]['surplus_num'] = 0;
                    $chatLists[$key]['is_limit'] = false;
                    // 如果没开启的，直接剩下0
                    if (0 == $status) {
                        $chatLists[$key]['surplus_num'] = 0;
                        $chatLists[$key]['is_limit'] = true;
                    } else if ($status && $dayLimit > 0) {
                        $chatLists[$key]['surplus_num'] = $value['day_limit'] - $useNum > 0 ? $value['day_limit'] - $useNum : 0;
                        $chatLists[$key]['is_limit'] = true;
                    }
                }
                return array_values($chatLists);
            } else {
                $channelLists = array_column($vectorLists, 'channel');
                // 机器人使用量
                $robotCount = KbRobotRecord::withTrashed()
                    ->where(['emb_model_id' => $channelLists, 'user_id' => $userId])
                    ->whereDay('create_time')
                    ->group('emb_model_id')
                    ->column('count(id) as count', 'emb_model_id');
                // 数据训练使用量
                $enbeddingCount = KbEmbedding::where(['emb_model_id' => $channelLists, 'user_id' => $userId])
                    ->where('status', '=', 2)
                    ->whereDay('create_time')
                    ->group('emb_model_id')
                    ->column('count(uuid) as count', 'emb_model_id');
                // 向量用量
                foreach ($vectorLists as $key => $value) {
                    $dayLimit = $value['day_limit'];
                    $status = $value['status'];
                    $useNum = ($robotCount[$value['channel']] ?? 0) + ($enbeddingCount[$value['channel']] ?? 0);
                    $vectorLists[$key]['use_num'] = $useNum;
                    $vectorLists[$key]['surplus_num'] = 0;
                    $vectorLists[$key]['is_limit'] = false;
                    // 如果没开启的，直接剩下0
                    if (0 == $status) {
                        $vectorLists[$key]['surplus_num'] = 0;
                        $vectorLists[$key]['is_limit'] = true;
                        // 开启的，但是有限制数量的
                    } else if ($status && $dayLimit > 0) {
                        $vectorLists[$key]['surplus_num'] = $value['day_limit'] - $useNum > 0 ? $value['day_limit'] - $useNum : 0;
                        $vectorLists[$key]['is_limit'] = true;
                    }
                }
                return array_values($vectorLists);
            }
        }
        // 绘画用量
        if (MemberPackageEnum::APPLY_DRAW == $type) {
            $channelLists = array_column($drawLists, 'channel');
            $drawCount = DrawRecords::withTrashed()
                ->where(['model' => $channelLists, 'user_id' => $userId])
                ->where('status', '<>', DrawEnum::STATUS_FAIL)
                ->whereDay('create_time')
                ->group('model')
                ->column('count(id) as count', 'model');
            // 绘画用量
            foreach ($drawLists as $key => $value) {
                $dayLimit = $value['day_limit'];
                $status = $value['status'];

                $useNum = $drawCount[$value['channel']] ?? 0;
                $drawLists[$key]['use_num'] = $useNum;
                $drawLists[$key]['surplus_num'] = 0;
                $drawLists[$key]['is_limit'] = false;

                // 如果没开启的，直接剩下0
                if (0 == $status) {
                    $drawLists[$key]['surplus_num'] = 0;
                    $drawLists[$key]['is_limit'] = true;
                } else if ($status && $dayLimit > 0) {
                    $drawLists[$key]['surplus_num'] = $value['day_limit'] - $useNum > 0 ? $value['day_limit'] - $useNum : 0;
                    $drawLists[$key]['is_limit'] = true;
                }
            }
            return array_values($drawLists);
        }
        // 音乐用量
        if (MemberPackageEnum::APPLY_MUSIC == $type) {

            $musicCount = MusicTask::withTrashed()
                ->where(['user_id' => $userId])
                ->where('status', '<>', MusicEnum::STATUS_FAIL)
                ->whereDay('create_time')
                ->count();
            // 音乐用量
            foreach ($musicLists as $key => $value) {
                $dayLimit = $value['day_limit'];
                $status = $value['status'];

                $useNum = $musicCount;
                $musicLists[$key]['use_num'] = $useNum;
                $musicLists[$key]['surplus_num'] = 0;
                $musicLists[$key]['is_limit'] = false;

                if (0 == $status) {
                    $musicLists[$key]['surplus_num'] = 0;
                    $musicLists[$key]['is_limit'] = true;
                } else if ($status && $dayLimit > 0) {
                    $musicLists[$key]['surplus_num'] = $value['day_limit'] - $useNum > 0 ? $value['day_limit'] - $useNum : 0;
                    $musicLists[$key]['is_limit'] = true;
                }
            }
            return $musicLists;
        }
        // 思维导图用量
        if (MemberPackageEnum::APPLY_MINDMAP == $type){
            $channel = ConfigService::get('mindmap_config', 'channel_id','');
            $model = ConfigService::get('mindmap_config', 'model_id', '');

            $subModel = (new ModelsCost())
                ->field(['id,model_id,channel,alias,name,price,status'])
                ->where(['type'=>ChatEnum::MODEL_TYPE_CHAT])
                ->where(['model_id'=>$channel,'id'=>$model])
                ->findOrEmpty()
                ->toArray();

            // 普通对话使用量
            $chatCount = ChatRecord::withTrashed()
                ->where(['chat_model_id' => $subModel['model_id'], 'user_id' => $userId,'type'=>ChatRecordEnum::CHAT_MINDMAP])
                ->whereDay('create_time')
                ->group('chat_model_id')
                ->column('count(id) as count', 'chat_model_id');
            // 对话用量
            foreach ($mindmapLists as $key => $value) {
                $dayLimit = $value['day_limit'];
                $status = $value['status'];
                $useNum = $chatCount[$subModel['model_id']] ?? 0;
                $mindmapLists[$key]['use_num'] = $useNum;
                $mindmapLists[$key]['surplus_num'] = 0;
                $mindmapLists[$key]['is_limit'] = false;
                $mindmapLists[$key]['channel'] = $subModel['model_id'];
                // 如果没开启的，直接剩下0
                if (0 == $status) {
                    $mindmapLists[$key]['surplus_num'] = 0;
                    $mindmapLists[$key]['is_limit'] = true;
                } else if ($status && $dayLimit > 0) {
                    $mindmapLists[$key]['surplus_num'] = $value['day_limit'] - $useNum > 0 ? $value['day_limit'] - $useNum : 0;
                    $mindmapLists[$key]['is_limit'] = true;
                }
            }
            return array_values($mindmapLists);
        }
        // 视频用量
        if (MemberPackageEnum::APPLY_VIDEO == $type){
            $videoCount = VideoRecord::withTrashed()
                ->where(['user_id' => $userId])
                ->where('status', '<>', VideoEnum::STATUS_FAIL)
                ->whereDay('create_time')
                ->count();
            // 音乐用量
            foreach ($videoLists as $key => $value) {
                $dayLimit = $value['day_limit'];
                $status = $value['status'];

                $useNum = $videoCount;
                $videoLists[$key]['use_num'] = $useNum;
                $videoLists[$key]['surplus_num'] = 0;
                $videoLists[$key]['is_limit'] = false;

                if (0 == $status) {
                    $videoLists[$key]['surplus_num'] = 0;
                    $videoLists[$key]['is_limit'] = true;
                } else if ($status && $dayLimit > 0) {
                    $videoLists[$key]['surplus_num'] = $value['day_limit'] - $useNum > 0 ? $value['day_limit'] - $useNum : 0;
                    $videoLists[$key]['is_limit'] = true;
                }
            }
            return $videoLists;
        }
        //ai搜索用量
        if(MemberPackageEnum::APPLY_AISEARCH == $type){
            $aisaerchCount = AiSearchRecord::withTrashed()
                ->where(['user_id' => $userId])
                ->whereDay('create_time')
                ->count();
            foreach ($aisearchLists as $key => $value){
                $dayLimit = $value['day_limit'];
                $status = $value['status'];

                $useNum = $aisaerchCount;
                $aisearchLists[$key]['use_num'] = $useNum;
                $aisearchLists[$key]['surplus_num'] = 0;
                $aisearchLists[$key]['is_limit'] = false;

                if (0 == $status) {
                    $aisearchLists[$key]['surplus_num'] = 0;
                    $aisearchLists[$key]['is_limit'] = true;
                } else if ($status && $dayLimit > 0) {
                    $aisearchLists[$key]['surplus_num'] = $value['day_limit'] - $useNum > 0 ? $value['day_limit'] - $useNum : 0;
                    $aisearchLists[$key]['is_limit'] = true;
                }
            }
            return $aisearchLists;
        }

    }

    /**
     * @notes 获取各个模型默认数据
     * @return array[]
     * @author cjhao
     * @date 2024/7/1 18:36
     */
    public static function getModels(){

        $modelsLists = Models::column('name,type,id');
        $chatModel = [];
        $vectorModel = [];
        $drawModel = [];
        $musicModel = [];
        $mindmapModel = [];
        $videoModel = [];
        $aisearchModel = [];
        foreach ($modelsLists as $model){
            if(MemberPackageEnum::APPLY_CHAT == $model['type']){
                $chatModel[] = [
                    'channel'   => $model['id'],
                    'name'      => $model['name'],
                    'day_limit' => '',
                    'status'    => 0,
                ];
            }else{
                $vectorModel[] = [
                    'channel'   => $model['id'],
                    'name'      => $model['name'],
                    'day_limit' => '',
                    'status'    => 0,
                ];
            }
        }
        $applyLists = MemberPackageEnum::getApplyLissts();
        foreach ($applyLists as $key => $value){
            switch ($key) {
                case DrawEnum::API_SD:
                    $drawModel[] = [
                        'channel'   =>  $key,
                        'name'      =>  $value,
                        'day_limit' =>  '',
                        'status'    =>  0,
                    ];
                    break;
                case 'music':
                    $musicModel[] = [
                        'channel'   =>  $key,
                        'name'      =>  $value,
                        'day_limit' =>  '',
                        'status'    =>  0,
                    ];
                    break;
                case 'mindmap':
                    $mindmapModel[] = [
                        'channel'   =>  $key,
                        'name'      =>  $value,
                        'day_limit' =>  '',
                        'status'    =>  0,
                    ];
                    break;
                case 'video':
                    $videoModel[] = [
                        'channel'   =>  $key,
                        'name'      =>  $value,
                        'day_limit' =>  '',
                        'status'    =>  0,
                    ];
                    break;
                case 'aisearch':
                    $aisearchModel[] = [
                        'channel'   =>  $key,
                        'name'      =>  $value,
                        'day_limit' =>  '',
                        'status'    =>  0,
                    ];
                    break;

            }
        }
        return [
            'chat_list'         => $chatModel,
            'vector_list'       => $vectorModel,
            'draw_list'         => $drawModel,
            'music_list'        => $musicModel,
            'mindmap_list'      => $mindmapModel,
            'video_list'        => $videoModel,
            'aisearch_list'     => $aisearchModel,
        ];
    }

}