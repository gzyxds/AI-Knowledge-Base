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

namespace app\api\logic\chat;

use app\common\enum\ChatEnum;
use app\common\enum\ChatRecordEnum;
use app\common\enum\CreationEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\logic\UserMemberLogic;
use app\common\model\chat\ChatRecord;
use app\common\model\chat\ChatRecordCategory;
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\model\creation\CreationModel;
use app\common\model\file\File;
use app\common\model\skill\Skill;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\service\ai\chat\AzureService;
use app\common\service\ai\chat\DoubaoService;
use app\common\service\ai\chat\MiniMaxService;
use app\common\service\ai\chat\OllamaService;
use app\common\service\ai\chat\QwenService;
use app\common\service\ai\chat\BaiduService;
use app\common\service\ai\chat\ZhipuService;
use app\common\service\ai\chat\OpenaiService;
use app\common\service\ai\chat\XunfeiService;
use app\common\service\ai\chat\SystemService;
use app\common\service\ai\ChatService;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\WordsService;
use Exception;

class ChatDialogLogic extends BaseLogic
{
    protected mixed $chatService;   // 对话实例类

    protected string $modelMainId;  // 主模型ID
    protected string $modelSubId;   // 子模型ID
    protected string $modelAlias;   // 模型别名
    protected string $channel;      // 模型渠道
    protected string $model;        // 模型名称
    protected string $price;        // 模型价格
    protected array $configs;       // 模型参数

    protected mixed $user       = null;     // 用户信息
    protected int $userId       = 0;        // 用户ID
    protected int $type         = 1;        // 对话类型: [1=简单对话, 2=创作对话,3=角色]
    protected int $creationType = 1;        // 对话类型:[1-常规,2-扩写,3-简写,4-续写,5-改写-正式得体,6-改写-严肃庄重,7-改写-轻松,8-改写-热情]
    protected int $otherId      = 0;        // 会话分类: [普通对话ID, 创作类型ID,角色ID]
    protected string|array $question = '';  // 提问问题
    protected string $file = '';            // 提问文件

    protected mixed $modelContent = null;  // 创作、角色表模型
    protected array $prompt       = [];    // 提示指令
    protected array|string $form  = [];    // 表单内容
    protected string $reply       = '';    // 回复内容
    protected array $messages     = [];    // 上下文内容
    protected array $correlation  = [];    // 相关的问题
    protected array $usage        = [];    // tokens消费信息

    protected int $replyType      = 1;     // 回复类型：1-模型回复；2-系统默认回复
    protected bool $isVipFree     = false; // 是否vip免费: false=不免费
    protected bool $isMultimodal  = false; // 是否支持多模态

    protected int $relatedIssuesNum = 0;   // 相关问题显示数

    /**
     * @notes 初始化
     * @param array $params
     * @param int $userId
     * @throws Exception
     * @author fzr
     */
    public function __construct(array $params, int $userId)
    {
        header('Access-Control-Allow-Origin: *');
        header('Connection: keep-alive');
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');

        // 基础参数
        $this->userId       = $userId;
        $this->question     = $params['question']??'';
        $this->otherId      = intval($params['other_id']??0);
        $this->type         = intval($params['type']??1);
        $this->creationType = intval($params['creation_type']?? 1);
        $this->file         = trim($params['file']??'');

        // 获取用户
        $this->user = (new User())->where(['id'=>$userId])->findOrEmpty();

        // 生成对话相关问题数量, 0=不生成, 改设置不对用户收费
        $this->relatedIssuesNum = intval(ConfigService::get('chat', 'related_issues_num', 0));

        // 如果是是思维导图，后台指定模型
        if(ChatRecordEnum::CHAT_MINDMAP == $this->type){
            $channel = ConfigService::get('mindmap_config', 'channel_id','');
            $model = ConfigService::get('mindmap_config', 'model_id', '');
            $subModel = (new ModelsCost())
                ->field(['id,model_id,channel,alias,name,price,status'])
                ->where(['type'=>ChatEnum::MODEL_TYPE_CHAT])
                ->where(['model_id'=>$channel,'id'=>$model])
                ->findOrEmpty()
                ->toArray();

            $this->otherId = 0;
            $subModel['price'] = ConfigService::get('mindmap_config', 'balance',0);
        }else{
            // 查询小模型
            $modelModelsCost = new ModelsCost();
            $subModel = $modelModelsCost
                ->field(['id,model_id,channel,alias,name,price,status'])
                ->where(['type'=>ChatEnum::MODEL_TYPE_CHAT])
                ->where(['id'=>intval($params['model'])])
                ->findOrEmpty()
                ->toArray();
        }

        // 模型验证
        if (!$subModel || !$subModel['status']) {
            $error = !$subModel ? '当前模型可能已被下架' : '当前模型已被下架了';
            ChatService::parseReturnError(true, $error);
            return;
        }

        // 查询大模型
        $mainModel = (new Models())->where(['id'=>$subModel['model_id']])->findOrEmpty()->toArray();
        if (!$mainModel || !$mainModel['is_enable']) {
            $error = !$mainModel ? '模型可能已被下架' : '模型已被下架了';
            ChatService::parseReturnError(true, $error);
            return;
        }
        // 模型使用
        $this->channel    = $subModel['channel'];
        $this->model      = $subModel['name'];
        $this->price      = $subModel['price'];
        $this->modelAlias = $subModel['alias'];
        $this->modelSubId = $subModel['id'];
        $this->modelMainId = $mainModel['id'];

        // 模型参数
        $this->configs = json_decode($mainModel['configs'], true);
        $this->configs['model']    = $this->model;
        $this->configs['channel']  = $this->channel;
        $this->configs['model_id'] = $mainModel['id'];

        // 是否开启默认回复
        $defaultReplyOpen = ConfigService::get('chat','default_reply_open', 0);
        if($defaultReplyOpen){
            $this->configs['default_reply'] = ConfigService::get('chat','default_reply', 0);
            $this->channel = 'system';
            $this->replyType = 2;
        }

        // 是否支持多模态
        if (in_array($mainModel['channel'], ['zhipu', 'openai', 'azure'])) {
            if (in_array($subModel['name'], [
                'glm-4v',
                'gpt-4o',
                'gpt-4o-2024-05-13',
                'gpt-4-vision-preview',
                'gpt-4-1106-vision-preview'])
            ) {
                $this->isMultimodal = true;
            }
        }
    }

    /**
     * @notes 发起对话
     * @throws Exception
     * @author fzr
     */
    public function chat(): void
    {
        try {
            // 验证参数
            $this->checkOtherId();
            $this->checkUser();

            // 选择渠道
            $this->chatService = match ($this->channel) {
                'openai',
                'baichuan'  => (new OpenaiService($this->configs)),
                'xunfei'  => (new XunfeiService($this->configs)),
                'zhipu'   => (new ZhipuService($this->configs)),
                'baidu'   => (new BaiduService($this->configs)),
                'qwen'    => (new QwenService($this->configs)),
                'azure'   => (new AzureService($this->configs)),
                'doubao'  => (new DoubaoService($this->configs)),
                'ollama'  => (new OllamaService($this->configs)),
                'minimax' => (new MiniMaxService($this->configs)),
                'system'  => (new SystemService($this->configs)),
                default => throw new Exception('渠道配置错误')
            };

            // 设置参数
            $this->setChatParams();

            // 敏感词验证
            WordsService::sensitive($this->question);

            // 问题审核(百度)
            WordsService::askCensor($this->question);

            // 发起对话
            $this->chatService->chatSseRequest($this->messages);

            // 获取回复内容
            $this->reply = $this->chatService->getReplyContent()[0];

            // 消耗的tokens
            $this->usage = $this->chatService->getUsage();

            // 生成相关问题
            $chatData = '';
            $chatEvent = 'finish';
            if ($this->relatedIssuesNum) {
                $chatEvent = 'question';
                $questions = ChatService::makeQuestion($this->chatService, $this->messages, $this->reply, $this->relatedIssuesNum);
                $chatData = json_encode($questions, JSON_UNESCAPED_UNICODE);
                $this->correlation = $questions;
            }
            ChatService::parseReturnSuccess($chatEvent, '', $chatData, 0, $this->model, 'stop');

            // 记录用户信息
            $model = new User();
            $model->startTrans();
            try {
                $this->saveChatRecord();
                $model->commit();
            } catch (Exception $e) {
                $model->rollback();
                throw new Exception($e->getMessage());
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            if (str_starts_with($error, 'Invalid image')) {
                $error = '网络原因,大模型无法解析此图片 ('.$e->getMessage().')';
            }
            ChatService::parseReturnError(
                true,
                $error,
                $e->getCode(),
                $this->model
            );
        }
    }

    /**
     * @notes 设置对话参数
     * @throws Exception
     * @author fzr
     */
    private function setChatParams(): void
    {
        // 创作类型处理
        $inputAsk = '';
        if (ChatRecordEnum::CHAT_CREATION == $this->type || ChatRecordEnum::CHAT_SKILL == $this->type){
            if($this->question && ChatRecordEnum::CHAT_CREATION == $this->type){
                if(1 == $this->creationType){
                    $this->form = $this->question;
                    $this->question = '';

                    // 对话问题替换,方便保存到数据库
                    $this->question = $this->modelContent['content'];
                    foreach ($this->modelContent['form'] as $formVal) {
                        $field = $formVal['props']['field']??'';
                        $form  = $this->form[$field] ?? '';
                        if ($formVal['props']['isRequired'] && empty($form)) {
                            throw new Exception('请输入：'.$formVal['props']['title'].'的内容');
                        }
                        if(is_array($form)){
                            $form = implode('、',$form);
                        }
                        $replaceStr     = '${'.$field.'}';
                        $this->question = str_replace($replaceStr,$form,$this->question);
                        $inputAsk .= $formVal['props']['title'].':'.$form.'；';

                    }
                }else{
                    $desc = CreationEnum::getCreationDesc($this->creationType);
                    $this->question = $desc.$this->question;
                }

            }
            $this->configs['temperature'] = floatval($this->modelContent['temperature']??0);
            $this->configs['context_num'] = floatval($this->modelContent['context_num']??0);
            if ($this->channel === 'openai' || $this->channel === 'api2d') {
                $this->configs['top_p']            = floatval($this->modelContent['top_p']??0);
                $this->configs['presence_penalty'] = floatval($this->modelContent['presence_penalty']??0);
            }
        }

        // 如果指令不为空,设置模型的指令
        if($this->modelContent){
            if($this->modelContent['system']){
                $this->prompt[0] = $this->modelContent['system'];
            }
            if(ChatRecordEnum::CHAT_SKILL == $this->type && $this->modelContent['content']){
                $this->prompt[] = $this->modelContent['content'];
            }
        }

        if (!empty($this->configs['global_directives'])) {
            $this->prompt[] = $this->configs['global_directives'];
        }

        // 处理全局指令
        foreach ($this->prompt as $prompt){
            switch (true) {
                case $this->chatService instanceof BaiduService:
                case $this->chatService instanceof QwenService:
                    // 支持单个指令
                    $this->messages[0] = ['role' => 'system', 'content' => $prompt];
                    break;
                default:
                    // 支持多个指令 (// 支持多个指令)
                    $this->messages[] = ['role' => 'system', 'content' => $prompt];
            }
        }

        // 如果是思维导图
        if (ChatRecordEnum::CHAT_MINDMAP == $this->type) {
            $inputAsk                     = $this->question;
            $this->configs['context_num'] = 0;
            $this->configs['n']           = 1;
            $cueWord                      = ConfigService::get('mindmap_config', 'cue_word');
            $prompt                       = str_replace('{prompt}', $this->question,$cueWord);
            $this->question               = $prompt;
        }

        // 联系上下文
        $contentNum = intval($this->configs['context_num']??0);
        if($contentNum > 0){
            // 查找对话内容
            $where[] = ['user_id','=',$this->userId];
            $where[] = ['is_show','=',1];
            if (ChatRecordEnum::CHAT_QUESTION == $this->type) {
                $where[] = ['category_id','=',$this->otherId];
                $where[] = ['other_id','=',0];
            } else {
                $where[] = ['other_id','=',$this->otherId];
                $where[] = ['category_id','=',0];
            }
            $chatRecords = (new ChatRecord())->where($where)
                ->limit($contentNum)
                ->order('id desc')
                ->select()->toArray();

            // 有联系上下文,处理对话记录
            $chatRecords = array_reverse($chatRecords);
            foreach ($chatRecords as $record){
                $ask = $record['ask'];
                if (is_array($ask)) {
                    $ask = implode('，',$ask);
                }
                $reply = $record['reply'];
                if (is_array($reply)) {
                    $reply = implode('，',$reply);
                }

                $filesPlugin = json_decode($record['files_plugin']??'[]', true);
                $imageUrl = FileService::getFileUrl($filesPlugin[0]['url']??'');
                if ($filesPlugin and $imageUrl and $this->isMultimodal) {
                    $this->messages[] = [
                        'role' => 'user',
                        'content' => [
                            ['type'=>'text', 'text'=>(string)$ask],
                            ['type'=>'image_url', 'image_url' => ['url'=>$imageUrl]],
                        ]
                    ];
                } else {
                    $this->messages[] = ['role'=>'user', 'content'=>(string)$ask];
                }
                $this->messages[] = ['role'=>'assistant', 'content'=>(string)$reply];
            }
        }

        if ($this->file and $this->isMultimodal) {
            $this->messages[] = [
                'role' => 'user',
                'content' => [
                    ['type'=>'text', 'text'=>$this->question],
                    ['type'=>'image_url', 'image_url' => ['url'=>$this->file]],
                ]
            ];
        } else {
            $this->messages[] = ['role' =>'user', 'content'=>$this->question];
        }

        if ($inputAsk) {
            $this->question = $inputAsk;
        }
    }

    /**
     * @notes 验证分类
     * @throws Exception
     * @author fzr
     */
    private function checkOtherId(): void
    {
        if (empty($this->type)) {
            throw new Exception('类型参数缺少');
        }

        // 验证会话id
        if (ChatRecordEnum::CHAT_QUESTION == $this->type) {
            if (empty($this->otherId)) {
                throw new Exception('请选择会话');
            }

            $recordCategory = (new ChatRecordCategory())->where(['id'=>$this->otherId, 'user_id'=>$this->userId])->findOrEmpty();
            if($recordCategory->isEmpty()){
                throw new Exception('会话不存在');
            }
        } else {

            if (ChatRecordEnum::CHAT_MINDMAP != $this->type && empty($this->otherId) && 1 == $this->creationType) {
                throw new Exception('请选择创作模型');
            }

            if(ChatRecordEnum::CHAT_CREATION == $this->type && 1 == $this->creationType){
                $creationModel = (new CreationModel())->where(['id' => $this->otherId])->findOrEmpty();
                if ($creationModel->isEmpty()) {
                    throw new Exception('创作模型不存在');

                }
                $this->modelContent = $creationModel;

            }
            if(ChatRecordEnum::CHAT_SKILL == $this->type){
                $skill = (new Skill())->where(['id' => $this->otherId])->findOrEmpty();
                if ($skill->isEmpty()) {
                    throw new Exception('角色不存在');

                }
                $this->modelContent = $skill;
            }


        }
    }

    /**
     * @notes 用户验证
     * @throws Exception
     * @author fzr
     */
    private function checkUser(): void
    {
        if ($this->user->isEmpty()) {
            throw new Exception('非法会员');
        }
        $type = MemberPackageEnum::APPLY_CHAT;
        if(ChatRecordEnum::CHAT_MINDMAP == $this->type){
            $type = MemberPackageEnum::APPLY_MINDMAP;
        }
        // is_limit=false (无限制次数), surplus_num=剩余次数
        $vips = UserMemberLogic::getUserPackageApply($this->userId,$type);
        foreach ($vips as $item) {
            if ($item['channel'] == $this->modelMainId) {
                if (!$item['is_limit'] || $item['surplus_num']) {
                    $this->isVipFree = true;
                    break;
                }
            }
        }

        // 最低消费验证
        $chatConfig = ConfigService::get('chat');
        $min_consume_status = $chatConfig['min_consume_status'] ?? 0;
        $min_consume_price = $chatConfig['min_consume_price'] ?? 0;
        $min_consume_tips = $chatConfig['min_consume_tips'] ?? '';
        if ($min_consume_status and $min_consume_price) {
            if ($this->user->balance < $min_consume_price) {
                throw new Exception($min_consume_tips?:'您当前余额低于系统最低消费限额,请前往充值中心充值');
            }
        }

        if (!$this->isVipFree) {
            if ($this->price && $this->user->balance <= 0) {
                throw new Exception('对话余额不足', 1100);
            }
        }

        if (YesNoEnum::YES == $this->user->is_blacklist) {
            throw new Exception('您已被管理员禁止提问,请联系客服详询原因。');
        }
    }

    /**
     * @notes 创建对话记录
     * @author fzr
     */
    private function saveChatRecord(): void
    {
        // Tokens消费信息
        $chatUseTokens = tokens_price('chat',  $this->modelSubId, $this->usage['str_length']);
        if ($this->isVipFree) {
            $chatUseTokens = 0;
        }

        if (ChatRecordEnum::CHAT_MINDMAP == $this->type) {
            $chatUseTokens = $this->price;
        }

        $flowsUsage = [
            'flows' => [
                [
                    'name'              => 'chat',
                    'model'             => $this->modelAlias,
                    'total_price'       => $chatUseTokens,
                    'prompt_tokens'     => $this->usage['prompt_tokens'],
                    'completion_tokens' => $this->usage['completion_tokens'],
                    'total_tokens'      => $this->usage['total_tokens'],
                    'str_length'        => $this->usage['str_length']
                ]
            ],
        ];

        // 图片理解
        $filesPlugin = [];
        if ($this->file) {
            $file = (new File())->where(['uri'=>FileService::setFileUrl($this->file)])->findOrEmpty()->toArray();
            $filesPlugin[] = [
                'type' => 'image',
                'name' => $file['name'] ?? '',
                'url'  => $file['uri']  ?? FileService::setFileUrl($this->file),
            ];
        }

        // 保存记录
        ChatRecord::create([
            'user_id'           => $this->userId,
            'category_id'       => ChatRecordEnum::CHAT_QUESTION == $this->type ? $this->otherId : 0,
            'other_id'          => in_array($this->type,[ChatRecordEnum::CHAT_CREATION,ChatRecordEnum::CHAT_SKILL]) ? $this->otherId : 0,
            'chat_model_id'     => $this->modelMainId,
            'ask'               => $this->question,
            'reply'             => $this->reply,
            'type'              => $this->type,
            'creation_type'     => $this->creationType,
            'extra'             => $this->form,
            'channel'           => $this->channel,
            'model'             => $this->modelAlias,
            'price'             => $chatUseTokens,
            'tokens'            => $this->usage['str_length'],
            'flows'             => json_encode($flowsUsage['flows'], JSON_UNESCAPED_UNICODE),
            'files_plugin'      => json_encode($filesPlugin, JSON_UNESCAPED_UNICODE),
            'correlation'       => json_encode($this->correlation, JSON_UNESCAPED_UNICODE),
            'reply_type'        => $this->replyType,
            'ip'                => request()->ip(),
        ]);

        // 如果是VIP则不是要扣费, 则扣除账户的费用
        if (ChatRecordEnum::REPLYTYPE_MODEL == $this->replyType and !$this->isVipFree) {
            // 账户扣费
            $changePrice = $this->user->balance - $chatUseTokens;
            $this->user->balance = max($changePrice, 0);
            $this->user->total_chat = $this->user->total_chat + 1;
            $this->user->save();

            // 钱包变动
            if ($chatUseTokens) {
                $changeType = AccountLogEnum::UM_DEC_CHAT;
                $changeAction = AccountLogEnum::DEC;
                UserAccountLog::add($this->userId, $changeType, $changeAction, $chatUseTokens, '', '', [], 0, $flowsUsage);
            }
        }
    }
}