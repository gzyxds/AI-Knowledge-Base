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

use app\common\enum\ChatEnum;
use app\common\enum\ChatRecordEnum;
use app\common\enum\draw\DrawEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\BaseLogic;
use app\common\logic\UserMemberLogic;
use app\common\model\chat\ChatRecord;
use app\common\model\chat\ChatRecordCategory;
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\model\creation\CreationModel;
use app\common\model\creation\CreationModelCollect;
use app\common\model\decorate\DecoratePage;
use app\common\model\user\User;
use app\common\model\Visitor;
use app\common\pgsql\KbEmbedding;
use app\common\service\ConfigService;
use app\common\service\FileService;
use Exception;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * index
 * Class IndexLogic
 * @package app\api\logic
 */
class IndexLogic extends BaseLogic
{
//    /**
//     * @notes 获取计费模型
//     * @return array
//     * @throws @\think\db\exception\DataNotFoundException
//     * @throws @\think\db\exception\DbException
//     * @throws @\think\db\exception\ModelNotFoundException
//     * @author fzr
//     */
//    public static function getAiModels(): array
//    {
//        $aiDefaultChannel = ConfigService::get('aiModel', 'channel', 'openai');
//        $emDefaultChannel = ConfigService::get('emModel', 'channel', 'openai');
//        $aiDefaultModel = ConfigService::get('aiModel', 'configs')[$aiDefaultChannel]['model']??'';
//        $emDefaultModel = ConfigService::get('emModel', 'configs')[$emDefaultChannel]['model']??'';
//
//        $model = new ModelsCost();
//        $chat = $model
//            ->field(['id,channel,name,alias,price,status'])
//            ->where(['type'=>ChatEnum::MODEL_TYPE_CHAT])
//            ->where(['status'=>1])
//            ->select()
//            ->toArray();
//
//        $emb = $model
//            ->field(['id,channel,name,alias,price,status'])
//            ->where(['type'=>ChatEnum::MODEL_TYPE_EMB])
//            ->where(['status'=>1])
//            ->select()
//            ->toArray();
//
//        // 处理默认选中(AI模型)
//        $chatChecked = false;
//        foreach ($chat as &$item) {
//            $item['checked'] = 0;
//            $item['price'] = format_amount_zero($item['price']);
//            if ($aiDefaultModel == $item['name']) {
//                $item['checked'] = 1;
//                $chatChecked = true;
//            }
//        }
//
//        if (!$chatChecked && $chat) {
//            $chat[0]['checked'] = 1;
//        }
//
//        // 处理默认选中(向量模型)
//        $embChecked = false;
//        foreach ($emb as &$item) {
//            $item['price'] = format_amount_zero($item['price']);
//            $item['checked'] = 0;
//            if ($emDefaultModel == $item['name']) {
//                $item['checked'] = 1;
//                $embChecked = true;
//            }
//        }
//
//        if (!$embChecked && $emb) {
//            $emb[0]['checked'] = 1;
//        }
//
//        return [
//            'chatList' => $chat,
//            'embList'  => $emb
//        ]??[];
//    }

    /**
     * @param int $userId
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function getAiModels(int $userId): array
    {
        $data = [];
        $model = new Models();
        $modelCost = new ModelsCost();
        foreach ([ChatEnum::MODEL_TYPE_CHAT, ChatEnum::MODEL_TYPE_EMB] as $type) {
            // 查询所有大模型
            $results = $model
                ->field(['id,channel,name,logo,remarks,is_default'])
                ->where(['type'=>$type])
                ->where(['is_enable'=>1])
                ->order('sort asc, id desc')
                ->select()
                ->toArray();

            // VIP模型查询
            $vips = [];
            $vipArray = UserMemberLogic::getUserPackageApply($userId, $type);
            foreach ($vipArray as $v) {
                $vips[$v['channel']] = [
                    'is_limit'    => $v['is_limit'],
                    'surplus_num' => $v['surplus_num'],
                ];
            }

            foreach ($results as &$item) {
                $item['logo'] = FileService::getFileUrl($item['logo']);

                $item['is_free'] = 0;
                $vip = $vips[$item['id']] ?? [];
                if ($vip and (!$vip['is_limit'] || $vip['surplus_num'])) {
                    $item['is_free'] = 1;
                }

                if (ChatEnum::MODEL_TYPE_CHAT == $type) {
                    $item['models'] = $modelCost
                        ->field(['id,name,alias,price'])
                        ->where(['type' => $type])
                        ->where(['model_id' => $item['id']])
                        ->where(['status' => 1])
                        ->order('sort asc, id desc')
                        ->withAttr(['price'=>function ($value) {
                            return format_amount_zero($value);
                        }])->withAttr(['support_image' => function ($value, $data) use($item) {
                            unset($value);
                            if (in_array($item['channel'], ['zhipu', 'openai', 'azure'])) {
                                if (in_array($data['name'], [
                                    'glm-4v',
                                    'gpt-4o',
                                    'gpt-4o-2024-05-13',
                                    'gpt-4-vision-preview',
                                    'gpt-4-1106-vision-preview'])
                                ) {
                                    return 1;
                                }
                            }
                            return 0;
                        }])
                        ->append(['support_image'])
                        ->select()
                        ->toArray();
                } else {
                    $m = $modelCost
                        ->field(['id,name,alias,price'])
                        ->where(['type' => $type])
                        ->where(['model_id' => $item['id']])
                        ->order('sort asc, id desc')
                        ->findOrEmpty()->toArray();

                    if ($m) {
                        unset($item['is_default']);
                        $item['name']  = $m['name'];
                        $item['alias'] = $m['alias'];
                        $item['price'] = format_amount_zero($m['price']);
                    } else {
                        unset($item);
                    }
                }
            }

            $scene = [ChatEnum::MODEL_TYPE_CHAT=>'chatModels', ChatEnum::MODEL_TYPE_EMB=>'vectorModels'];
            $data[$scene[$type]] = $results;
        }

        return $data;
    }

    /**
     * @notes 获取全局配置
     * @param $userId
     * @return array
     * @author 段誉
     */
    public static function getConfigData($userId): array
    {
        // 是否安装
        $install = file_exists(root_path() . '/config/install.lock');
        if (!$install) {
            return ['install'=>false]??[];
        }

        $sdConfig = ConfigService::get('draw_config', DrawEnum::API_SD, DrawEnum::getDrawDefaultConfig(DrawEnum::API_SD));
        $dalle3Config = ConfigService::get('draw_config', DrawEnum::API_DALLE3, DrawEnum::getDrawDefaultConfig(DrawEnum::API_DALLE3));
        $mjConfig = ConfigService::get('draw_config', DrawEnum::API_MJ_GOAPI, DrawEnum::getDrawDefaultConfig(DrawEnum::API_MJ_GOAPI));

        // 登录配置
        $loginConf = ConfigService::get('login');
        $loginConfig = [
            // 注册方式
            'register_way'        => $loginConf['register_way'] ?? config('project.login.register_way'),
            // 登录方式
            'login_way'           => ConfigService::get('login', 'login_way', config('project.login.login_way')),
            // 注册强制绑定手机
            'coerce_mobile' => ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile')),
            // 默认登录方式: 1-微信登录/公众号授权登录; 2-手机号登录; 3-邮箱登录
            'default_login_way'   => ConfigService::get('login', 'default_login_way', config('project.login.default_login_way')),
            // 短信验证码
            'register_sms_verify' => ConfigService::get('login', 'register_sms_verify', config('project.login.register_sms_verify')),
            // 政策协议
            'is_agreement'        => ConfigService::get('login', 'is_agreement', config('project.login.is_agreement'))
        ];

        // 网站信息
        $website = [
            'pc_name'     => ConfigService::get('website', 'pc_name'),
            'pc_title'    => ConfigService::get('website', 'pc_title'),
            'pc_logo'     => FileService::getFileUrl(ConfigService::get('website', 'pc_logo')),
            'pc_ico'      => FileService::getFileUrl(ConfigService::get('website', 'pc_ico')),
            'pc_login_image' => FileService::getFileUrl(ConfigService::get('website', 'pc_login_image', '')),
            'pc_desc'     => ConfigService::get('website', 'pc_desc', ''),
            'pc_key'      => ConfigService::get('website', 'pc_key', ''),
        ];

        // 备案信息
        $copyright = ConfigService::get('copyright', 'config', []);

        // 分享配置
        $shareImage = ConfigService::get('share', 'share_image',ConfigService::get('website', 'shop_logo'));
        $share = [
            'share_page'        => ConfigService::get('share', 'share_page',2),
            'share_title'       => ConfigService::get('share', 'share_title',''),
            'share_content'     => ConfigService::get('share', 'share_content',''),
            'share_image'       => empty($shareImage) ? '' : FileService::getFileUrl($shareImage),
        ];

        // 对话配置
        $chatConfig = ConfigService::get('chat');
        $defaultChatIcon = ConfigService::get('default_image','chat_logo');
        $chat = [
            'chat_logo'         => FileService::getFileUrl($chatConfig['chat_logo']??$defaultChatIcon),
            'price_unit'        => ($chatConfig['price_unit']??'')?:'电力值',
            'global_directives' => $defaultChatIcon['global_directives'] ?? '',
            'chat_limit_tips'   => $defaultChatIcon['chat_limit_tips']   ?? '今日对话次数已达上限',

            'is_reopen'         => $chatConfig['is_reopen'] ?? 0,//是否重开对话
            'default_reply_open'=> $chatConfig['default_reply_open'] ?? 0,//对话默认回复开关
            'default_reply'     => $chatConfig['default_reply'] ?? '',//对话默认回复内容
            'chat_title'        => $chatConfig['chat_title'] ?? '',//对话标题
            'watermark'         => $chatConfig['watermark'] ?? '内容由AI生成仅供参考',//水印
            'is_show_model'     => $chatConfig['is_show_model'] ?? 0,   // 模型显示
        ];

        // 判断是否需要重开聊天窗口
        if ($chat['is_reopen'] == 1) {
            $ChatCategory = ChatRecordCategory::where(['user_id'=>$userId])->order('id','desc')->findOrEmpty();
            if ($ChatCategory->isEmpty()) {
                $chat['is_reopen'] = 0;
            } else {
                $ChatRecords = ChatRecord::where(['user_id'=>$userId,'category_id'=>$ChatCategory->id,'is_show'=>1])->findOrEmpty();
                if ($ChatRecords->isEmpty()) {
                    $chat['is_reopen'] = 0;
                }
            }
        }

        // 开关配置
        $switch = [
            // PC端启用
            'pc_status'       => intval(ConfigService::get('pc','status', 1)),
            // 语音播报
            'voice_status'    => intval(ConfigService::get('voice_output','is_open', 0)),
            // 充值启用
            'recharge_status' => intval(ConfigService::get('recharge','status', 0)),
            // 账号注销
            'account_cancelled' => intval(ConfigService::get('user', 'is_cancelled', 1)),
            // 绘画开关
            'sd_status'       => intval($sdConfig['status']),
            'dalle3_status'   => intval($dalle3Config['status']),
            'mj_status'       => intval($mjConfig['status']),
            // AI搜索状态
            'search_status'  => intval(ConfigService::get('ai_search', 'status', 0))
        ];

        // 公告配置
        $bulletinConfig = [
            // 公告弹窗
            'is_bulletin'      => intval(ConfigService::get('bulletin', 'is_bulletin', 0)),
            // 公告内容
            'bulletin_content' => ConfigService::get('bulletin', 'bulletin_content', ''),
        ];

        // 人工客服
        $manualKf = ConfigService::get('manual_kf')??[];
        $manualKfConf = [
            'status'       => (int) ($manualKf['status']??0),
            'icons'        => isset($manualKf['icons'])   ? FileService::getFileUrl($manualKf['icons'])   : '',
            'qr_code'      => isset($manualKf['qr_code']) ? FileService::getFileUrl($manualKf['qr_code']) : '',
            'title'        => $manualKf['title']??['value'=>'', 'status'=>0],
            'phone'        => $manualKf['phone']??['value'=>'', 'status'=>0],
            'service_time' => $manualKf['service_time']??['value'=>'', 'status'=>0]
        ];

        // 在线客服
        $onlineKf = ConfigService::get('online_kf')??[];
        $onlineKfConf = [
            'status' => intval($onlineKf['status']??0),
            'link'   => trim($onlineKf['link']??''),
            'icons'  => isset($onlineKf['icons']) ? FileService::getFileUrl($onlineKf['icons']) : ''
        ];

        $squareConfig = [
            'draw_award'    => [
                'is_open'           => ConfigService::get('draw_award', 'is_open'),
//                'one_award'         => ConfigService::get('draw_award', 'one_award'),
//                'day_num'           => ConfigService::get('draw_award', 'day_num'),
//                'auto_audit'        => ConfigService::get('draw_award', 'auto_audit'),
            ],
            'music_award'       => [
                'is_open'           => ConfigService::get('music_award', 'is_open'),
//                'one_award'         => ConfigService::get('music_award', 'one_award'),
//                'day_num'           => ConfigService::get('music_award', 'day_num'),
//                'auto_audit'        => ConfigService::get('music_award', 'auto_audit'),
            ],
            'video_award'       => [
                'is_open'           => ConfigService::get('video_award', 'is_open'),
//                'one_award'         => ConfigService::get('video_award', 'one_award'),
//                'day_num'           => ConfigService::get('video_award', 'day_num'),
//                'auto_audit'        => ConfigService::get('music_award', 'auto_audit'),
            ],
            'robot_award'       => [
               'is_open'            => ConfigService::get('robot_award', 'is_open')
            ],
        ];
        return [
            'install'                   => true,
            'share'                     => $share,
            'domain'                    => FileService::getFileUrl('', '', true),
            'current_domain'            => request()->domain(true),
            'version'                   => config('project.version'),
            'login'                     => $loginConfig,
            'website'                   => $website,
            'copyright'                 => $copyright,
            'bulletin'                  => $bulletinConfig,
            'switch'                    => $switch,
            'chat'                      => $chat,
            'manualKf'                  => $manualKfConf,
            'onlineKf'                  => $onlineKfConf,
            'square_config'             => $squareConfig,
            'distribution'              => [
                'is_open' =>    ConfigService::get('distribution','is_open')
            ],
            'card_code'                 => [
                'is_show'   =>  ConfigService::get('card_code','is_show'),
                'buy_site'  =>  ConfigService::get('card_code','buy_site'),
                'is_open'   => ConfigService::get('card_code','is_open',0),
            ],
            'member_package_open'       => ConfigService::get('member_package','is_open',1)
        ]??[];
    }

    /**
     * @notes 知识库空间
     * @param int $userId
     * @param int $knowId
     * @return array
     * @throws DbException
     * @author fzr
     */
    public static function space(int $userId, int $knowId): array
    {
        // 总容量
        $totalSpace = (new User())->where(['id'=>$userId])->value('total_space');

        // 已使用容量
        $useSpace = (new KbEmbedding())->where(['kb_id'=>$knowId, 'is_delete'=>0])->count();

        // 总共已使用
        $totalUseSpace = (new KbEmbedding())->where(['user_id'=>$userId, 'is_delete'=>0])->count();

        return [
            'total_space' => $totalSpace,
            'total_use_space' => $totalUseSpace,
            'know_use_space' => $useSpace
        ];
    }

    /**
     * @notes 页面装修数据
     * @param $id
     * @param $userId
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author fzr
     */
    public static function getDecorate($id,$userId): array
    {
        $decorate =  (new DecoratePage())
            ->field(['type', 'name', 'data'])
            ->findOrEmpty($id)
            ->toArray();
        if(7 == $decorate['type']){
            $dataLists = json_decode($decorate['data'],true);
            $dataLists = array_column($dataLists,null,'name');
            foreach ($dataLists as $key => $data){
                if('index-hot' != $key){
                    continue;
                }
                $dataType = $data['content']['dataType'];
                $dataNum = $data['content']['dataNum'];
                //系统推荐
                if(1 == $dataType){
                    // 使用人数
                    $creationAllUserCount = ChatRecord::where(['type'=> ChatRecordEnum::CHAT_CREATION])
                        ->group('other_id')
                        ->order('user_count desc')
                        ->column('COUNT(DISTINCT user_id) AS user_count','other_id');
                    $createIds = array_keys($creationAllUserCount);
                    $creationModelLists = [];
                    if ($createIds) {
                        $creationModelLists = CreationModel::where(['id' => $createIds, 'status' => 1])
                            ->field('id,name,tips,image,virtual_use_num')
                            ->limit($dataNum)
                            ->orderRaw('FIELD(id,' . implode(',', $createIds) . ')')
                            ->select()->toArray();
                    }
                    $diffNum = $dataNum - count($creationModelLists);
                    //查询出来的数据不能满足次数，重新获取
                    if($diffNum > 0){
                        $createModelNumLists = CreationModel::where('id','not in',$createIds)
                            ->where(['status'=>1])
                            ->order('sort desc,id desc')
                            ->field('id,name,tips,image,virtual_use_num')
                            ->limit($diffNum)
                            ->select()
                            ->toArray();
                        $creationModelLists = array_merge($creationModelLists,$createModelNumLists);
                    }
                }else{
                    //用户自己选择
                    $createIds = array_column($data['content']['data'],'id');
                    $creationModelLists = CreationModel::where(['id'=>$createIds,'status'=>1])
                                    ->field('id,name,tips,image,virtual_use_num')
                                    ->orderRaw('FIELD(id,' . implode(',', $createIds) . ')')
                                    ->select()->toArray();
                }
                //查询使用人数和收藏人数、是否收藏
                $ids = array_column($creationModelLists,'id');
                $creationAllUserCount = ChatRecord::where(['type'=> ChatRecordEnum::CHAT_CREATION,'other_id'=>$ids])
                    ->group('other_id')
                    ->column('COUNT(DISTINCT user_id) AS user_count','other_id');

                $creationCollectCount = CreationModelCollect::where(['creation_id'=>$ids])
                            ->group('creation_id')
                            ->column('COUNT(user_id) AS creation_count','creation_id');
                $collectCreation = [];
                if($userId){
                    $collectCreation = CreationModelCollect::where(['user_id'=>$userId])
                                            ->column('creation_id');
                }
                foreach ($creationModelLists as $key => $createModel){
                    $creationModelLists[$key]['user_count'] = $creationAllUserCount[$createModel['id']] ?? 0 ;
                    $creationModelLists[$key]['user_count'] +=  $createModel['virtual_use_num'];
                    $creationModelLists[$key]['collect_count'] = $creationCollectCount[$createModel['id']] ?? 0;
                    $creationModelLists[$key]['is_collect'] = in_array($createModel['id'],$collectCreation);
                }
            }
            if(isset($dataLists['index-hot'])){
                $dataLists['index-hot']['content']['data'] = $creationModelLists;
            }
            $decorate['data'] = json_encode(array_values($dataLists));
        }
        return $decorate;

    }

    /**
     * @notes 获取政策协议
     * @param string $type
     * @return array
     * @author 段誉
     */
    public static function getPolicyByType(string $type): array
    {
        return [
            'title'   => ConfigService::get('agreement', $type . '_title', ''),
            'content' => ConfigService::get('agreement', $type . '_content', '')
        ]??[];
    }

    /**
     * @notes 增加访客记录
     * @return bool
     * @author fzr
     */
    public static function visit(): bool
    {
        try {
            $params = request()->post();
            if (!isset($params['terminal']) || !in_array($params['terminal'], UserTerminalEnum::ALL_TERMINAL)) {
                throw new Exception('终端参数缺失或有误');
            }

            // 一个ip一个终端一天只生成一条记录
            $ip =  request()->ip();
            $record = (new Visitor())
                ->where([
                    'ip' => $ip,
                    'terminal' => $params['terminal']
                ])->whereDay('create_time')
                ->findOrEmpty();

            // 增加访客在终端的浏览量
            if (!$record->isEmpty()) {
                $record->visit += 1;
                $record->save();
                return true;
            }

            // 生成访客记录
            Visitor::create([
                'ip' => $ip,
                'terminal' => $params['terminal'],
                'visit' => 1
            ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 获取思维导图配置
     * @param int $userId
     * @return array
     * @author cjhao
     * @date 2024/6/18 18:36
     */
    public static function mindMapConfig(int $userId): array
    {
        $data = [
            'balance'               => ConfigService::get('mindmap_config', 'balance',0),
            'cue_word'              => ConfigService::get('mindmap_config', 'cue_word'),
            'is_example'            => ConfigService::get('mindmap_config', 'is_example'),
            'example_content'       => ConfigService::get('mindmap_config', 'example_content'),
            'member_free'           => false,
        ];
        $userMember = UserMemberLogic::getUserPackageApply($userId,MemberPackageEnum::APPLY_MINDMAP);
        $mindmapData = $userMember[0] ?? [];
        if($mindmapData && ($mindmapData['surplus_num'] > 0 || false === $mindmapData['is_limit'])){
            $data['member_free'] = true;
        }
        return $data;
    }
}