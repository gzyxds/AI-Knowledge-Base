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

use app\common\enum\ChatEnum;
use app\common\enum\kb\KnowEnum;
use app\common\enum\kb\RobotEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\UserMemberLogic;
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\model\file\File;
use app\common\model\kb\KbKnow;
use app\common\model\kb\KbRobot;
use app\common\model\kb\KbRobotInstruct;
use app\common\model\kb\KbRobotPublish;
use app\common\model\kb\KbRobotRecord;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\pgsql\KbEmbedding;
use app\common\service\ai\chat\AzureService;
use app\common\service\ai\chat\DoubaoService;
use app\common\service\ai\chat\MiniMaxService;
use app\common\service\ai\chat\OllamaService;
use app\common\service\ai\chat\OpenaiService;
use app\common\service\ai\chat\SystemService;
use app\common\service\ai\chat\XunfeiService;
use app\common\service\ai\chat\ZhipuService;
use app\common\service\ai\chat\BaiduService;
use app\common\service\ai\chat\QwenService;
use app\common\service\ai\ChatService;
use app\common\service\ai\VectorService;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\WordsService;
use Exception;

/**
 * 机器人对话服务类
 */
class KbChatService
{
    protected bool $isGlobalDirectives = false;

    protected bool $stream = true;
    protected mixed $chatService;          // 对话实例类
    protected mixed $user  = null;         // 用户信息
    protected mixed $robot = null;         // 机器人

    protected array $kbIds = [];           // 知识库ID
    protected string $embChannel='';       // 向量渠道
    protected int $embModelId=0;           // 向量主模型ID
    protected string $embModel='';         // 向量模型
    protected string $embAlias='';         // 向量别名
    protected string $embPrice='0';        // 向量价格
    protected array $embUsage=[];          // 向量tokens信息

    protected string $channel;             // 模型渠道
    protected string $modelMainId;         // 主模型ID
    protected string $modelSubId;          // 子模型ID
    protected string $modelAlias;          // 模型别名
    protected string $model;               // 模型名称
    protected string $price;               // 模型价格
    protected array $configs;              // 模型参数

    protected int $userId      = 0;        // 用户ID
    protected int $cateId      = 0;        // 会话的ID
    protected int $robotId     = 0;        // 机器人ID
    protected int $squareId    = 0;        // 广场的ID
    protected string $question = '';       // 提问问题
    protected string $file = '';           // 提问文件
    protected bool $isMultimodal  = false; // 是否支持多模态

    protected int $shareId          = 0;   // 分享的ID
    protected string $shareApiKey   = '';  // 分享的密钥
    protected string $shareIdentity = '';  // 分享的身份

    protected array $messages     = [];    // 上下文内容
    protected array $quotes       = [];    // 引用的数据
    protected array $usage        = [];    // 消耗的Tokens
    protected array $correlation  = [];    // 相关的问题
    protected string $reply       = '';    // 回复的内容
    protected array $images       = [];    // 附带的图片
    protected array $video        = [];    // 附带的视频
    protected array $files        = [];    // 附带的文件

    protected bool $instruct      = false; // 菜单指令回复

    protected bool $chatVip = false; // 对话模型是否是VIP
    protected bool $embVip  = false; // 向量模型是否是VIP

    protected int $defaultReplyOpen = 0;        // 默认回复
    protected string $defaultReplyContent = ''; // 默认回复内容

    /**
     * @notes 初始化
     * @param array $params
     * @param int $userId
     * @param bool $stream
     * @throws Exception
     * @author fzr
     */
    public function __construct(array $params, int $userId, bool $stream=true)
    {
        if ($stream) {
            header('Access-Control-Allow-Origin: *');
            header('Connection: keep-alive');
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('X-Accel-Buffering: no');
        }

        // 基础参数
        $this->userId   = $userId;
        $this->question = $params['question'];
        $this->squareId = intval($params['square_id']??0);
        $this->robotId  = intval($params['robot_id']??0);
        $this->cateId   = intval($params['cate_id']??0);
        $this->file     = trim($params['file']??'');
        $this->stream = $stream;

        // 分享参数
        $this->shareId       = $params['share_id']    ?? 0;
        $this->shareApiKey   = $params['apiKey']      ?? '';
        $this->shareIdentity = $params['identity']    ?? '';
        $shareContextNum     = $params['context_num'] ?? 0;

        // 查询机器人
        $modelKbRobot = new KbRobot();
        $this->robot = $modelKbRobot->where(['id'=>$this->robotId])->findOrEmpty()->toArray();
        if (!$this->robot || !$this->robot['is_enable']) {
            $error = !$this->robot ? '机器人不存在了!' : '机器人已被禁用了!';
            throw new Exception($error);
        }

        // 查询知识库
        $know = null;
        if ($this->robot['kb_ids']) {
            $this->kbIds = explode(',', $this->robot['kb_ids']);
            $know = (new KbKnow())->whereIn('id', $this->kbIds)->findOrEmpty()->toArray();
            if (!$know) {
                $this->robot['kb_ids'] = '';
                $this->kbIds = [];
            }
        }

        // 查询小模型
        $modelModelsCost = new ModelsCost();
        $subModels = $modelModelsCost->where(['type'=>ChatEnum::MODEL_TYPE_CHAT])->where(['id'=>$this->robot['model_sub_id']])->findOrEmpty()->toArray();
        if (!$subModels || !$subModels['status']) {
            $error = !$subModels ? ($this->robot['model_sub_id'] ? '对话模型可能已被下架了' : '请配置机器人对话模型') : '对话模型已被下架了';
            throw new Exception($error);
        }

        // 查询大模型
        $mainModel = (new Models())->where(['id'=>$subModels['model_id']])->findOrEmpty()->toArray();
        if (!$mainModel || !$mainModel['is_enable']) {
            $error = !$mainModel ? '对话模型已被下架!' : '对模型已被下架了!';
            throw new Exception($error);
        }

        $this->modelMainId = $mainModel['id'];
        $this->modelSubId  = $subModels['id'];
        $this->modelAlias  = $subModels['alias'];
        $this->channel     = $subModels['channel'];
        $this->price       = $subModels['price'];
        $this->model       = $subModels['name'];
        $this->configs = json_decode($mainModel['configs'], true);
        $this->configs['channel'] = $this->channel;
        $this->configs['model'] = $this->model;
        $this->configs['model_id'] = $subModels['model_id'];

        // 向量模型
        if ($know) {
            $mainEmb = (new Models())->where(['type'=>ChatEnum::MODEL_TYPE_EMB])->where(['id'=>$know['embedding_model_id']])->findOrEmpty();
            if ($mainEmb->isEmpty() || !$mainEmb->is_enable) {
                throw new Exception('向量模型已被下架了');
            }

            //$embModels = $modelModelsCost->where(['type'=>ChatEnum::MODEL_TYPE_EMB])->where(['id'=>$know['embedding_model_sub_id']])->findOrEmpty()->toArray();
            $embModels = $modelModelsCost->where(['type'=>ChatEnum::MODEL_TYPE_EMB])->where(['model_id'=>$mainEmb['id']])->order('status desc')->findOrEmpty()->toArray();
            if (!$embModels) {
                throw new Exception('向量模型已被下架了: ' . $mainEmb['name']);
            }
            $this->embModelId = $mainEmb['id'];
            $this->embChannel = $embModels['channel'];
            $this->embModel   = $embModels['name'];
            $this->embAlias   = $embModels['alias'];
            $this->embPrice   = $embModels['price'];
        }

        // VIP验证
        $this->chatVip = $this->checkVip($this->modelMainId, MemberPackageEnum::APPLY_CHAT);
        $this->embVip  = $this->checkVip($this->embModelId, MemberPackageEnum::APPLY_VECTOR);

        // 查询用户
        if ($userId) {
            $this->user = (new User())->where(['id'=>$userId])->findOrEmpty();
            if ($this->user->isEmpty() || $this->user->is_blacklist || $this->user->is_disable) {
                $error = $this->user->isEmpty() ? '用户异常' : '当前用户已被拉黑';
                throw new Exception($error);
            }

            // 最低消费验证
            $chatConfig = ConfigService::get('chat')??[];
            $min_consume_status = intval($chatConfig['min_consume_status'] ?? 0);
            $min_consume_price = $chatConfig['min_consume_price'] ?? 0;
            $min_consume_tips = $chatConfig['min_consume_tips'] ?? '';
            if ($min_consume_status and $min_consume_price) {
                if ($this->user->balance < $min_consume_price) {
                    throw new Exception($min_consume_tips?:'您当前余额低于系统最低消费限额,请前往充值中心充值');
                }
            }

            if (!$this->chatVip and !$this->embVip) {
                if (($this->embPrice || $this->price) and $this->user->balance <= 0) {
                    throw new Exception('账户余额不足!');
                }
            }
        }

        // 分享的上下文数
        if ($this->shareApiKey) {
            $this->configs['context_num'] = $shareContextNum;
        }

        // 是否开启默认回复
        $this->defaultReplyOpen = ConfigService::get('chat','default_reply_open', 0);
        if($this->defaultReplyOpen){
            $this->defaultReplyContent = ConfigService::get('chat','default_reply', '');
            $this->channel = 'system';
        }

        // 是否支持多模态
        if (in_array($mainModel['channel'], ['zhipu', 'openai', 'azure'])) {
            if (in_array($subModels['name'], [
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
     * 验证模型是不是VIP免费
     *
     * @param int $modelId  (主模型ID: 对话模型/向量模型)
     * @param int $type (1=对话模型, 2=向量模型)
     * @return bool
     */
    public function checkVip(int $modelId, int $type): bool
    {
        // is_limit=false (无限制次数), surplus_num=剩余次数
        $vips = UserMemberLogic::getUserPackageApply($this->userId, $type);
        foreach ($vips as $item) {
            if ($item['channel'] == $modelId) {
                if (!$item['is_limit'] || $item['surplus_num']) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * @notes 发起对话
     * @throws Exception
     * @author fzr
     */
    public function chat(): array
    {
        try {
            // 敏感词验证
            WordsService::sensitive($this->question);

            // 问题审核(百度)
            WordsService::askCensor($this->question);

            // 针对指令回复
            $chatResult = [];
            $instruct = $this->getChatInstruct();
            if ($instruct) {
                $this->instruct = true;
                if ($this->stream) {
                    foreach ($instruct as $item) {
                        ChatService::parseReturnSuccess($item['event'], time(), $item['data'], 0, $this->model);
                    }
                } else {
                    $d = $instruct[0];
                    $chatResult = ChatService::parseReturnSuccess($d['event'], time(), $d['data'], 0, $this->model, "", false, $this->usage);
                }
            }

            // 针对GPT回复
            if (!$instruct) {
                if (!$this->defaultReplyOpen) {
                    $this->messages = $this->getPgEmbedding();
                }
                if (!$this->messages) {
                    // 不使用AI
                    $this->instruct = true;
                    $this->reply       = $this->robot['search_empty_text'] ?: '我无法理解哦~';
                    $this->reply       = $this->defaultReplyOpen ? $this->defaultReplyContent : $this->reply;
                    $prompt_tokens     = gpt_tokenizer_count($this->question);
                    $completion_tokens = gpt_tokenizer_count($this->question.$this->reply);
                    $str_length = mb_strlen($this->question.$this->reply);
                    $this->usage = [
                        'prompt_tokens'     => $prompt_tokens,
                        'completion_tokens' => $completion_tokens,
                        'total_tokens'      => $prompt_tokens + $completion_tokens,
                        'str_length'        => $str_length
                    ];
                    if ($this->stream) {
                        ChatService::parseReturnSuccess('finish', time(), $this->reply, 0, $this->model);
                    } else {
                        $chatResult = ChatService::parseReturnSuccess('finish', time(), $this->reply, 0, $this->model, '', false, $this->usage);
                    }
                } else {
                    // 选择渠道
                    $this->messages = $this->getChatContext($this->messages[0]??[]);
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
                        default   => throw new Exception('模型配置错误了: ' . $this->channel)
                    };

                    // 发起对话
                    if ($this->stream) {
                        $this->chatService->chatSseRequest($this->messages);
                    } else {
                        $chatResult = $this->chatService->chatHttpRequest($this->messages);
                    }

                    // 图片输出
                    $images = array_map(function($item) {
                        return ['url' => FileService::getFileUrl($item['url']), 'name' => $item['name']];
                    }, $this->images);
                    if ($images && $this->stream) {
                        ChatService::parseReturnSuccess('image', time(), json_encode($images), 0, $this->model);
                    }

                    // 视频输出
                    $files = array_map(function($item) {
                        return ['url' => FileService::getFileUrl($item['url']), 'name' => $item['name']];
                    }, $this->video);
                    if ($files && $this->stream) {
                        ChatService::parseReturnSuccess('video', time(), json_encode($files), 0, $this->model);
                    }

                    // 附件输出
                    $files = array_map(function($item) {
                        return ['url' => FileService::getFileUrl($item['url']), 'name' => $item['name']];
                    }, $this->files);
                    if ($files && $this->stream) {
                        ChatService::parseReturnSuccess('file', time(), json_encode($files), 0, $this->model);
                    }

                    // 获取回复内容
                    $this->reply = $this->chatService->getReplyContent()[0];
                    $this->usage = $this->chatService->getUsage();

                    // 相似问题输出
                    if ($this->stream) {
                        $chatData = '';
                        $chatEvent = 'finish';
                        if ($this->robot['related_issues_num']) {
                            $chatEvent = 'question';
                            $questions = ChatService::makeQuestion($this->chatService, $this->messages, $this->reply, $this->robot['related_issues_num']);
                            $chatData = json_encode($questions, JSON_UNESCAPED_UNICODE);
                            $this->correlation = $questions;
                        }
                        ChatService::parseReturnSuccess($chatEvent, '', $chatData, 0, $this->model, 'stop');
                    }
                }
            }

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

            // 非流式的返回
            if (!$this->stream) {
                return $chatResult;
            }
        } catch (Exception $e) {
            $err = ChatService::parseReturnError(
                $this->stream,
                $e->getMessage(),
                $e->getCode(),
                $this->model
            );

            if (!$this->stream) {
                return $err;
            }
        }
        return [];
    }

    /**
     * @notes 获取对话上下文
     * @throws Exception
     * @author fzr
     */
    private function getChatContext($pgEmb = []): array
    {
        $messages = [];

        // 全局指令
        if (!empty($this->configs['global_directives'])) {
            $this->isGlobalDirectives = true;
            $messages[] = ['role'=>'system', 'content'=>$this->configs['global_directives']];
        }

        // 角色指令
        if ($this->robot['roles_prompt']) {
            $messages[] = ['role' => 'system', 'content' => $this->robot['roles_prompt']];
        }

        $contextNum = intval($this->configs['context_num']??0);
        if (!$contextNum) {
            return $messages;
        }

        $where[] = ['robot_id', '=', $this->robotId];
        $where[] = ['category_id', '=', $this->cateId];
        if ($this->shareApiKey) {
            // 分享发布的
            $where[] = ['share_id', '=', $this->shareId];
            $where[] = ['share_apikey', '=', $this->shareApiKey];
            $where[] = ['share_identity', '=', $this->shareIdentity];
        } else {
            // 普通对话的
            $where[] = ['user_id', '=', $this->userId];
        }

        // 从广场来了
        if ($this->squareId) {
            $where[] = ['square_id', '=', $this->squareId];
        }

        $context_num = intval($this->configs['context_num']??0);
        if ($context_num <= 0) {
            return $messages;
        }

        $modelRecord = new KbRobotRecord();
        $chatRecords = $modelRecord
            ->where($where)
            ->where(['is_show'=>1])
            ->limit($context_num)
            ->order('id desc')
            ->select()
            ->toArray();

        $chatRecords = array_reverse($chatRecords);
        foreach ($chatRecords as $record){
            $ask = $record['ask'];
            if (is_array($ask)) {
                $ask = implode('，', $ask);
            }

            $reply = $record['reply'];
            if (is_array($reply)) {
                $separator = '，';
                $reply = implode($separator, $reply);
            }
            // $messages[] = ['role'  => 'user','content' => (string)$ask];
            // $messages[] = ['role'  => 'assistant','content' => (string)$reply];

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
                $messages[] = ['role'=>'user', 'content'=>strval($ask)];
            }
            $messages[] = ['role'=>'assistant', 'content'=>strval($reply)];
        }

        if ($this->file and $this->isMultimodal) {
            $messages[] = [
                'role' => 'user',
                'content' => [
                    ['type'=>'text', 'text'=>$pgEmb['content']??$this->question],
                    ['type'=>'image_url', 'image_url' => ['url'=>$this->file]],
                ]
            ];
        } else {
            $messages[] = ['role' =>'user', 'content'=>$pgEmb['content']??$this->question];
        }

        return $messages;
    }

    /**
     * @notes 获取引用的内容
     * @throws Exception
     * @author fzr
     */
    private function getPgEmbedding(): array
    {
        try {
            $pgLists = [];
            if ($this->embModelId) {
                // GPT转向量
                $vectorService = new VectorService($this->embModelId);
                $embeddingArr = $vectorService->toEmbedding($this->embChannel, $this->embModel, $this->question);
                $embeddingStr = '[' . implode(',', $embeddingArr) . ']';
                $this->embUsage = $vectorService->getUsage();

                // 匹配的规则
                $symbol = '=';
                $orders = 'score asc';
                $wheres = 'pe.embedding <=> \':embeddings\' <= :similarity';

                // 查询相似度
                $modelKbEmbedding = new KbEmbedding();
                $sql = $modelKbEmbedding
                    ->alias('pe')
                    ->field('pe.uuid,pe.question,pe.answer,pe.annex,(pe.embedding <' . $symbol . '> :embedding) AS score')
                    ->whereIn('pe.kb_id', $this->kbIds)
                    ->where(['pe.status' => KnowEnum::RUN_OK])
                    //->where(['pe.model' => $this->embModel])
                    ->where(['pe.dimension' => count($embeddingArr)])
                    ->where(['pe.is_delete' => 0])
                    ->whereRaw($wheres)
                    ->bind(['embedding' => $embeddingStr])
                    ->order($orders)
                    ->limit(intval($this->robot['search_limits']))
                    ->buildSql();

                $searchSimilarity = number_format(1 - $this->robot['search_similarity'], 3);

                // 执行数据查询
                $sql = str_replace(":embeddings", $embeddingStr, $sql);
                $sql = str_replace(":similarity", $searchSimilarity, $sql);
                $sql = str_replace("( SELECT", "SET LOCAL hnsw.ef_search = 100;\n(SELECT", $sql);
                $pgLists = app('db')->connect('pgsql')->query($sql);

                // 处理数据格式
                foreach ($pgLists as &$p) {
                    $p['score'] = trim($p['score'], '-');
                    $p['score'] = number_format(1 - $p['score'], 5);
                }
            }

            if (!$pgLists && $this->robot['search_empty_type'] == RobotEnum::EMPTY_ANSWER_NULL) {
                // 空检索: 固定文本回复
                return [];
            } elseif (!$pgLists && $this->robot['search_empty_type'] == RobotEnum::EMPTY_ANSWER_AI) {
                // 空检索: 使用AI回复
                $messages[] = ['role' => 'user', 'content' => $this->question];
                return $messages;
            } else {
                // 保存引用数据
                foreach ($pgLists as $pgItem) {
                    $score = ltrim($pgItem['score'], '-');
                    $this->quotes[] = [
                        'uuid'     => $pgItem['uuid'],
                        'score'    => $score,
                        'answer'   => $pgItem['answer'],
                        'question' => $pgItem['question'],
                    ];
                    $annex = json_decode($pgItem['annex'], true);
                    $this->images = array_merge($this->images, $annex['images']??[]);
                    $this->video  = array_merge($this->video, $annex['video']??[]);
                    $this->files  = array_merge($this->files, $annex['files']??[]);
                }

                // 处理引用数据
                $documents = "";
                foreach ($pgLists as $pgItem) {
                    if ($documents) {
                        $documents .= "\n------\n\n";
                    }
                    $documents .= $pgItem['question'] . "\n";
                    if ($pgItem['answer']) {
                        $documents .= $pgItem['answer'] . "\n";
                    }
                }

                $dataStr = "<"."Data".">" . "</"."Data".">";
                $content = "使用 $dataStr 标记中的内容作为你的知识:\n\n";
                $content .= "<"."Data".">\n[[documents]]". "</"."Data".">\n";

                // 调教要求拼接
                $content .= "\n回答要求：
            - 如果你不清楚答案，你需要澄清。
            - 避免提及你是从 $dataStr 获取的知识
            - 保持答案与 $dataStr 中描述的一致。
            - 使用 Markdown 语法优化回答格式。
            - 使用与问题相同的语言回答。\n
            问题: [[question]]";

                // 返回上下文
                $content = str_replace("            ", "", $content);
                $content = str_replace("[[documents]]", $documents, $content);
                $content = str_replace("[[question]]", '"""'.$this->question.'"""', $content);
                $messages[] = ['role' => 'user', 'content' => $content];
                return $messages;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 获取对话指令
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    private function getChatInstruct(): array
    {
        $modelKbRobotInstruct = new KbRobotInstruct();
        $instruct = $modelKbRobotInstruct
            ->field(['id,keyword,content,images'])
            ->where(['robot_id'=>$this->robotId])
            ->select()
            ->toArray();

        $hit = [];
        foreach ($instruct as $item) {
            if ($item['keyword'] == $this->question) {
                $hit = $item;
            }
        }

        $data = [];
        if ($hit) {
            $data[] = ['event'=>'chat', 'data'=>$hit['content']];

            $imgLists = [];
            $dataList = [];
            $images = $hit['images'] ? explode(',', $hit['images']) : [];
            foreach ($images as $img) {
                $imgLists[] = ['url'=>$img, 'name'=>$img];
                $dataList[] = ['url'=>FileService::getFileUrl($img), 'name'=>$img];
            }

            if ($dataList) {
                $data[] = ['event'=>'image', 'data'=>json_encode($dataList)];
            }

            $data[] = ['event'=>'finish', 'data'=>''];
            $this->reply  = $hit['content'];
            $this->images = $imgLists;
        }
        if ($data) {
            $prompt_tokens     = gpt_tokenizer_count($this->question);
            $completion_tokens = gpt_tokenizer_count($this->question.$hit['content']);
            $str_length = mb_strlen($this->question.$hit['content']);
            $this->usage = [
                'prompt_tokens'     => $prompt_tokens,
                'completion_tokens' => $completion_tokens,
                'total_tokens'      => $prompt_tokens + $completion_tokens,
                'str_length'        => $str_length
            ];
        }
        return $data??[];
    }

    /**
     * @notes 保存对话记录
     * @author fzr
     */
    private function saveChatRecord(): void
    {
        // 上下文组
        $context = $this->messages;
        $context[] = ['role'=>'assistant', 'content'=>$this->reply];
        if ($this->isGlobalDirectives) {
            array_shift($context);
        }

        // 图片理解
        $filesPlugin = [];
        if ($this->file) {
            $fileModel = new File();
            $file = $fileModel->where(['uri'=>FileService::setFileUrl($this->file)])->findOrEmpty()->toArray();
            $filesPlugin[] = [
                'type' => 'image',
                'name' => $file['name'] ?? '',
                'url'  => $file['uri']  ?? FileService::setFileUrl($this->file),
            ];
        }

        // 对话Tokens
        $chatUseTokens = tokens_price('chat', $this->modelSubId, $this->usage['str_length']);
        $chatUseTokens = $this->chatVip ? 0 : $chatUseTokens;
        $chatUseTokens = $this->defaultReplyOpen ? 0 : $chatUseTokens; // 默认回复不收费
        $flowsUsage = [
            'robotId'     => $this->robot['id'],
            'robotName'   => $this->robot['name'],
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
            ]
        ];

        // 向量Tokens
        $embUseTokens = 0;
        if ($this->embUsage) {
            $embUseTokens = tokens_price('emb', $this->embModelId, $this->embUsage['str_length']);
            $embUseTokens = $this->embVip ? 0 : $embUseTokens;
            $embUseTokens = $this->defaultReplyOpen ? 0 : $embUseTokens; // 默认回复不收费
            $flowsUsage['flows'][] = [
                'name'              => 'emb',
                'model'             => $this->embAlias,
                'total_price'       => $embUseTokens,
                'prompt_tokens'     => $this->embUsage['prompt_tokens'],
                'completion_tokens' => $this->embUsage['completion_tokens'],
                'total_tokens'      => $this->embUsage['total_tokens'],
                'str_length'        => $this->embUsage['str_length']
            ];
        }

        // 用户扣费 (菜单指令不需要扣费)
        $changeAmount = 0;
        if (!$this->instruct and (!$this->chatVip || !$this->embVip)) {
            if (!$this->chatVip) {
                $changeAmount += $chatUseTokens;
            }

            if (!$this->embVip) {
                $changeAmount += $embUseTokens;
            }

            $balance = $this->user->balance - $changeAmount;

            User::update([
                'balance' => max($balance, 0)
            ], ['id' => $this->userId]);

            // 扣费日志
            if ($changeAmount) {
                $changeType = AccountLogEnum::UM_DEC_ROBOT_CHAT;
                $changeAction = AccountLogEnum::DEC;
                $changeRemark = AccountLogEnum::getChangeTypeDesc($changeType);
                UserAccountLog::add($this->userId, $changeType, $changeAction, $changeAmount, '', $changeRemark, [], 0, $flowsUsage);
            }
        }

        // 提问数量
        User::update([
            'total_chat' => ['inc', 1]
        ], ['id'=>$this->userId]);

        // 保存记录
        $userId = $this->shareId ? 0 : $this->userId;
        KbRobotRecord::create([
            'user_id'        => $userId,
            'robot_id'       => $this->robotId,
            'category_id'    => $this->cateId,
            'square_id'      => $this->squareId,
            'chat_model_id'  => $this->modelMainId,
            'emb_model_id'   => $this->embModelId,
            'ask'            => $this->question,
            'reply'          => $this->reply,
            'images'         => json_encode($this->images, JSON_UNESCAPED_UNICODE),
            'video'          => json_encode($this->video, JSON_UNESCAPED_UNICODE),
            'files'          => json_encode($this->files, JSON_UNESCAPED_UNICODE),
            'quotes'         => json_encode($this->quotes, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'context'        => json_encode($context, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'correlation'    => json_encode($this->correlation, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'flows'          => json_encode($flowsUsage['flows'], JSON_UNESCAPED_UNICODE),
            'files_plugin'   => json_encode($filesPlugin, JSON_UNESCAPED_UNICODE),
            'model'          => $this->modelAlias,
            'tokens'         => $changeAmount,
            'share_id'       => $this->shareId,
            'share_apikey'   => $this->shareApiKey,
            'share_identity' => $this->shareIdentity
        ]);

        // 分享更新
        if ($this->shareId) {
            (new KbRobotPublish())
                ->where(['id' => $this->shareId])
                ->where(['robot_id' => $this->robotId])
                ->update([
                    'use_count' => ['inc', 1],
                    'use_time'  => time()
                ]);
        }
    }
}