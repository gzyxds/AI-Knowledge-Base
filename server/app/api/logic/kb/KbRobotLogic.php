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

namespace app\api\logic\kb;

use app\common\enum\kb\KbRotBotSquareEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\BaseLogic;
use app\common\logic\NoticeLogic;
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\model\kb\KbDigital;
use app\common\model\kb\KbKnow;
use app\common\model\kb\KbRobot;
use app\common\model\kb\KbRobotCategory;
use app\common\model\kb\KbRobotInstruct;
use app\common\model\kb\KbRobotShareLog;
use app\common\model\kb\KbRobotSquare;
use app\common\model\kb\KbRobotVisitor;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\service\ConfigService;
use app\common\service\FileService;
use Exception;
use think\facade\Config;
use think\facade\Db;

/**
 * 机器人管理逻辑类
 */
class KbRobotLogic extends BaseLogic
{

    /**
     * @notes 机器人详情
     * @param int $id
     * @param int $userId
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public static function detail(int $id, int $userId): array
    {
        $modelKbRobot = new KbRobot();
        $detail = $modelKbRobot
            ->withoutField('sort,limit_prompt,delete_time')
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();

        if ($detail) {
            if (!$detail['is_public'] && $detail['user_id'] !== $userId) {
                throw new Exception('无权限使用');
            }

            unset($detail['user_id']);
            $detail['kb_ids']       = $detail['kb_ids'] ? explode(',', $detail['kb_ids']) : [];
            $detail['icons']        = FileService::getFileUrl($detail['icons']);
            $detail['model_id']     = $detail['model_id'] ?: '';
            $detail['model_sub_id'] = $detail['model_sub_id'] ?: '';

            // 快捷菜单
            $modelKbRobotInstruct = new KbRobotInstruct();
            $detail['menus'] = $modelKbRobotInstruct
                ->field(['keyword,content,images'])
                ->where(['robot_id'=>$id])
                ->order('id asc')
                ->select()
                ->toArray();

            foreach ($detail['menus'] as &$item) {
                $images = $item['images'] ? explode(',', $item['images']) : [];
                foreach ($images as &$img) {
                    $img = FileService::getFileUrl($img);
                }
                $item['images'] = $images;
            }

            // 关联知识库
            if ($detail['kb_ids']) {
                $kbIds = (new KbKnow())->whereIn('id', $detail['kb_ids'])->column('id');
                $okExistIds = [];
                $noExistIds = [];
                foreach ($detail['kb_ids'] as $kid) {
                    if (!in_array($kid, $kbIds)) {
                        $noExistIds[] = $kid;
                    } else {
                        $okExistIds[] = $kid;
                    }
                }
                $detail['kb_ids'] = $okExistIds;
                if ($noExistIds) {
                    KbRobot::update([
                        'kb_ids' => implode(',', $okExistIds)
                    ], ['id'=>$id]);
                }
            }

            // 关联数字人
            $detail['digital'] = [];
            if ($detail['digital_id'] && $detail['is_digital']) {
                $detail['digital'] = (new KbDigital())
                    ->withoutField('user_id,channel,dubbing,delete_time')
                    ->where(['id'=>$detail['digital_id']])
                    ->findOrEmpty()
                    ->toArray();
            }
        }

        self::addVisit(1, $id);
        return $detail;
    }

    /**
     * @ntoes 机器人新增
     * @param array $post
     * @param int $userId
     * @return bool|array
     * @author fzr
     */
    public static function add(array $post, int $userId): bool|array
    {
        $model = new KbRobot();
        $model->startTrans();
        try {
            // 查机器人
            $userInfo = (new User())->field(['robot_num'])->where(['id'=>$userId])->findOrEmpty();

            // 验证数量
            if ($userInfo->robot_num <= 0) {
                throw new Exception('机器人数量不足,创建失败!');
            }

            // 默认图标
            $iconImage = FileService::getFileUrl(Config::get('project.default_image.robot_icon'));
            $chatImage = FileService::getFileUrl(Config::get('project.default_image.robot_chat'));
            if (!empty($post['image'])) {
                $iconImage = FileService::setFileUrl($post['image']);
            }

            // 创建机器人
            $botCode = generate_sn(KbRobot::class, 'code', $userId);
            $robot = KbRobot::create([
                'user_id'            => $userId,
                'code'               => $botCode,
                'kb_ids'             => '',
                'icons'              => $chatImage,
                'image'              => $iconImage,
                'name'               => $post['name'] ?? '默认智能体',
                'intro'              => $post['intro'] ?? '',
                'sort'               => 0,
                'roles_prompt'       => '',
                'limit_prompt'       => '',
                'search_similarity'  => 0.4,
                'search_limits'      => 3,
                'search_empty_type'  => 1,
                'search_empty_text'  => '',
                'welcome_introducer' => '',
                'digital_bg'         => '#ffffff',
                'digital_id'         => 0,
                'is_digital'         => 0,
                'is_public'          => 0,
                'is_show_context'    => 1,
                'is_show_quote'      => 1,
                'create_time'        => time(),
                'update_time'        => time()
            ]);

            $flowUsage = [
                'robotId'   => $robot['id'],
                'robotName' => $robot['name']
            ];

            // 创建记录
            $changeAmount = 1;
            $changeType   = AccountLogEnum::ROBOT_DEC_CREATE;
            $changeRemark = AccountLogEnum::getChangeTypeDesc($changeType);
            UserAccountLog::add($userId, $changeType, AccountLogEnum::DEC, $changeAmount, '', $changeRemark, [], 0, $flowUsage);

            // 扣减数量
            User::update([
                'robot_num'   => ['dec', 1],
                'update_time' => time()
            ], ['id'=>$userId]);

            $model->commit();
            return ['id'=>$robot['id']]??[];
        } catch (Exception $e) {
            $model->rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 机器人编辑
     * @param array $post
     * @param int $userId
     * @return bool
     * @author fzr
     */
    public static function edit(array $post, int $userId): bool
    {
        $model = new KbRobot();
        $model->startTrans();
        try {
            // 机器人检测
            $robot = $model->field(['id,is_enable'])
                ->where(['id'=>intval($post['id'])])
                ->where(['user_id'=>$userId])
                ->findOrEmpty();

            if (!$robot || !$robot['is_enable']) {
                $errMsg = !$robot ? '机器人不存在了' : '机器人被禁用,禁止操作!';
                throw new Exception($errMsg);
            }

            // 知识库检测
            if (($post['kb_ids']??[])) {
                $modelKnow = new KbKnow();
                $knows = $modelKnow
                    ->field(['id,name,embedding_model_id,is_enable'])
                    ->whereIn('id', $post['kb_ids'])
                    ->select()
                    ->toArray();

                if (count($post['kb_ids']) !== count($knows)) {
                    throw new Exception('检测到知识库存在变动,请刷新后再试!');
                }

                $vectorModels = $knows[0]['embedding_model_id']??0;
                foreach ($knows as $item) {
                    if (!$item['is_enable']) {
                        throw new Exception($item['name'].': 知识库已被禁用!');
                    }
                    if ($item['embedding_model_id'] !== $vectorModels) {
                        throw new Exception('请选择相同向量模型的知识库!');
                    }
                }
            }

            // 主模型检测
            $mainModel = (new Models())->where(['id'=>intval($post['model_id'])])->findOrEmpty();
            if (!$mainModel || !$mainModel['is_enable']) {
                throw new Exception('主模型已被下架!');
            }

            // 子模型检测
            $subModel = (new ModelsCost())->where(['id'=>intval($post['model_sub_id'])])->findOrEmpty();
            if (!$subModel || !$subModel['status']) {
                throw new Exception('子模型已被下架!');
            }

            if ($subModel['model_id'] != $mainModel['id']) {
                throw new Exception('模型匹配关系异常');
            }

            // 自定义菜单
            $menus = [];
            foreach (($post['menus']??[]) as $item) {
                $images = [];
                foreach (($item['images']??[]) as $img) {
                    $images[] = FileService::setFileUrl($img);
                }
                $menus[] = [
                    'user_id'  => $userId,
                    'robot_id' => $robot['id'],
                    'keyword'  => $item['keyword'],
                    'content'  => $item['content'],
                    'images'   => implode(',', $images)
                ];
            }

            KbRobot::update([
                'kb_ids'             => implode(',', $post['kb_ids']),
                'icons'              => FileService::setFileUrl($post['icons']??''),
                'image'              => $post['image'],
                'name'               => $post['name'],
                'intro'              => $post['intro']??'',
                // 'model'              => $post['model']??'',
                'model_id'           => intval($post['model_id']),
                'model_sub_id'       => intval($post['model_sub_id']),
                'sort'               => intval($post['sort']??0),
                'cate_id'            => intval($post['cate_id']??0),
                'roles_prompt'       => trim($post['roles_prompt']??''),
                'limit_prompt'       => trim($post['limit_prompt']??''),
                'search_similarity'  => floatval($post['search_similarity']),
                'search_limits'      => intval($post['search_limits']),
                'search_empty_type'  => intval($post['search_empty_type']),
                'search_empty_text'  => trim($post['search_empty_text']??''),
                'welcome_introducer' => trim($post['welcome_introducer']??''),
                'related_issues_num' => trim($post['related_issues_num']??0),
                'copyright'          => $post['copyright']??'',
                'digital_bg'         => trim($post['digital_bg']??''),
                'digital_id'         => intval($post['digital_id']??0),
                'is_digital'         => intval($post['is_digital']??0),
                'is_public'          => intval($post['is_public']??0),
                'is_show_context'    => $post['is_show_context'],
                'is_show_quote'      => $post['is_show_quote'],
                'update_time'        => time()
            ], ['id'=>intval($post['id'])]);

            $modelKbRobotInstruct = new KbRobotInstruct();
            $modelKbRobotInstruct
                ->where(['user_id'=>$userId])
                ->where(['robot_id'=>$robot['id']])
                ->delete();

            $modelKbRobotInstruct->saveAll($menus);

//            $isPublic = intval($post['is_public']??0);
//            if ($isPublic) {
//                $square = (new KbRobotSquare())
//                    ->where(['robot_id'=>intval($post['id'])])
//                    ->where(['user_id'=>$userId])
//                    ->findOrEmpty()->toArray();
//                if (!$square) {
//                    KbRobotSquare::create([
//                        'user_id'  => $userId,
//                        'robot_id' => intval($post['id']),
//                        'cate_id'  => intval($post['cate_id']??0),
//                        'is_show'  => 1
//                    ]);
//                } else {
//                    KbRobotSquare::update([
//                        'cate_id' => intval($post['cate_id']??0),
//                    ], ['id'=>$square['id']]);
//                }
//            } else {
//                (new KbRobotSquare())
//                    ->where(['robot_id'=>intval($post['id'])])
//                    ->where(['user_id'=>$userId])
//                    ->update([
//                        'delete_time' => time()
//                    ]);
//            }

            $model->commit();
            return true;
        } catch (Exception $e) {
            $model->rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 机器人删除
     * @param int $id
     * @param int $userId
     * @return bool
     * @author fzr
     */
    public static function del(int $id, int $userId): bool
    {
        $model = new KbRobot();
        $model->startTrans();
        try {
            // 验证机器人
            $robot = $model->field(['id,name,is_enable'])
                ->where(['id'=>$id])
                ->where(['user_id'=>$userId])
                ->findOrEmpty();

            if (!$robot) {
                throw new Exception('机器人不存在了!');
            }

            // 发起删除
            KbRobot::destroy($id);

            $flowUsage = [
                'robotId'   => $robot['id'],
                'robotName' => $robot['name']
            ];

            // 创建记录
            $changeAmount = 1;
            $changeType   = AccountLogEnum::ROBOT_INC_DELETE;
            $changeRemark = AccountLogEnum::getChangeTypeDesc($changeType);
            UserAccountLog::add($userId, $changeType, AccountLogEnum::INC, $changeAmount, '', $changeRemark, [], 0, $flowUsage);

            // 扣减数量
            User::update([
                'robot_num'   => ['inc', 1],
                'update_time' => time()
            ], ['id'=>$userId]);

            $model->commit();
            return true;
        } catch (Exception $e) {
            $model->rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 新增访问记录
     * @param int $terminal
     * @param int $robotId
     * @return bool
     * @author fzr
     */
    public static function addVisit(int $terminal, int $robotId): bool
    {
        try {
            $ip =  request()->ip();

            // 一个ip一个终端一天只生成一条记录
            $record = (new KbRobotVisitor())
                ->where(['ip' => $ip])
                ->where(['robot_id' => $robotId])
                ->where(['terminal' => $terminal])
                ->whereDay('create_time')
                ->findOrEmpty();

            // 增加访客在终端的浏览量
            if (!$record->isEmpty()) {
                $record->visit += 1;
                $record->save();
                return true;
            }

            // 生成访客记录
            KbRobotVisitor::create([
                'ip'       => $ip,
                'robot_id' => $robotId,
                'terminal' => $terminal,
                'visit'    => 1
            ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2024/7/25 11:29
     */
    public static function categoryLists(){
        $lists = KbRobotCategory::where(['is_enable'=>1])
            ->withoutField('update_time,create_time,delete_time,is_enable,sort')
            ->select()->toArray();
        return $lists;
    }

    /**
     * @notes 机器人分享
     * @param array $params
     * @param array $userInfo
     * @return bool
     * @author cjhao
     * @date 2024/7/25 10:39
     */
    public static function share(array $params,array $userInfo)
    {
        try {
            Db::startTrans();
            $robotId = $params['id'] ?? 0;
            $cateId = $params['cate_id'] ?? 0;
            if(empty($robotId)){
                throw new Exception('请选择智能体');
            }
            $isOpen     = ConfigService::get('robot_award', 'is_open');
            $autoAudit  = ConfigService::get('robot_award', 'auto_audit');
            $oneAward   = 0;
//            if (!$isOpen) {
//                throw new Exception('智能体分享未开启，请联系管理员');
//            }
            $robot = KbRobot::where(['user_id'=>$userInfo['user_id'],'id'=>$robotId])->findOrEmpty();
            if($robot->isEmpty()){
                throw new Exception('智能体不存在，请重新选择');
            }
            $shareLog = KbRobotShareLog::where(['user_id'=>$userInfo['user_id'],'robot_id'=>$robotId])
                ->findOrEmpty();
            //第一次分享获取的奖励
            if($autoAudit){
                $robot->is_public = 1;
                $robot->save();
                if($shareLog->isEmpty() && $isOpen){
                    $dayNum   = ConfigService::get('robot_award', 'day_num');
                    $oneAward   = ConfigService::get('robot_award', 'one_award');
                    $shareNum = KbRobotSquare::where(['user_id'=>$userInfo['user_id'],'verify_status'=>1])
                        ->whereDay('create_time')
                        ->count();
                    if ($dayNum > $shareNum  && $oneAward) {
                        User::update(['balance'=>['inc',$oneAward]],['id'=>$userInfo['user_id']]);
                        // 记录账户流水
                        UserAccountLog::add(
                            $userInfo['user_id'],
                            AccountLogEnum::UM_INC_ROBTO_SHARE,
                            AccountLogEnum::INC,
                            $oneAward
                        );
                        $unit = ConfigService::get('chat', 'price_unit', '电力值');
                        BaseLogic::$returnData = '分享成功,获取'.format_amount_zero($oneAward).$unit;
                    }

                }
            }


            $square = KbRobotSquare::create([
                'user_id'  => $userInfo['user_id'],
                'robot_id' => $robotId,
                'cate_id'  => $cateId,
                'verify_status'=>$autoAudit,
                'is_show'  => $autoAudit
            ]);
            //记录日志
            KbRobotShareLog::create([
                'square_id' => $square['id'],
                'user_id'  => $userInfo['user_id'],
                'robot_id' => $robotId,
                'channel' => $userInfo['terminal'],
//                'cate_id'  => $cateId,
                'balance'  => $oneAward,
                'is_show'  => 1
            ]);
            if(1 == $autoAudit){
                //添加信息通知
                NoticeLogic::addSquareNotice(
                    $userInfo['user_id'],
                    7,
                    1,
                    [
                        'square_id'     => $square->id,
                        'robot_id'      => $robotId,
                        'verify_status' => 1,
                        'verify_result' => '',
                        'balance'       => $oneAward
                    ]
                );


            }
            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 取消分享
     * @param $params
     * @param $userId
     * @return bool
     * @author cjhao
     * @date 2024/7/26 16:38
     */
    public static function cancelShare(array $params,int $userId){
        try{
            Db::startTrans();
            (new KbRobotSquare())
                ->where(['robot_id'=>intval($params['id'])])
                ->where(['user_id'=>$userId])
                ->update([
                    'delete_time' => time()
                ]);
            KbRobot::where(['user_id'=>$userId,'id'=>$params['id']])->update(['is_public'=>0]);
            Db::commit();
            return true;
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


}