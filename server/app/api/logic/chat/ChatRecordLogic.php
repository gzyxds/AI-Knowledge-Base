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

use app\common\enum\ChatRecordEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\ChatRecord;
use app\common\model\chat\ChatRecordCollect;
use Exception;

/**
 * 对话记录逻辑类
 */
class ChatRecordLogic extends BaseLogic
{
    /**
     * @notes 会话清除
     * @param array $params
     * @return bool
     * @author fzr
     */
    public static function chatClean(array $params): bool
    {
        try {
            if (isset($params['id']) && $params['id'] != '') {
                ChatRecord::update(['is_show' => 0], ['id'=>intval($params['id'])]);
            } else {
                ChatRecord::update(
                    ['is_show' => 0],
                    [
                        'user_id'     => $params['user_id'],
                        'category_id' => $params['category_id'] ?? 0,
                        'is_show'     => 1,
                        'type'        => $params['type'],
                        'other_id'    => $params['other_id'] ?? 0
                    ]
                );
            }

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 收藏会话
     * @param array $params
     * @return bool
     * @author fzr
     */
    public static function collectCreate(array $params): bool
    {
        try {
            ChatRecordCollect::create([
                'user_id'    => $params['user_id'],
                'records_id' => $params['records_id']
            ]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 收藏取消
     * @param $params
     * @return bool
     * @author fzr
     */
    public static function collectCancel($params): bool
    {
        try {
            $model = new ChatRecordCollect();
            $model->where(['id'=>$params['collect_id']])->delete();
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 更新回复
     * @param array $params
     * @param int $userId
     * @return bool
     * @author cjhao
     * @date 2024/6/14 10:21
     */
    public static function update(array $params,int $userId)
    {
        try {
            $detail = (new ChatRecord())->where(['id'=>$params['id'],'user_id'=>$userId])
                ->findOrEmpty();
            if(ChatRecordEnum::CHAT_CREATION != $detail['type']){
                throw new Exception('回复内容不可以更新');
            }

            $detail->reply = $params['content'];
            $detail->save();
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 对话记录删除
     * @param $params
     * @param $userId
     * @return string|void
     * @author cjhao
     * @date 2024/8/13 11:51
     */
    public static function del($params,$userId)
    {
        $id = $params['id'] ?? '';
        $content = $params['content'] ?? 0;
        if(empty($id)){
            return '请选择记录';
        }
        $chatRecord = ChatRecord::where(['id'=>$id,'type'=>2,'user_id'=>$userId])
            ->findOrEmpty();
        if($chatRecord->isEmpty()){
            return '记录不存在';
        }
        $reply = $chatRecord->reply;
        if(!is_array($reply)){
            $chatRecord->delete();
        }else{
            unset($reply[$content]);
            if(0 == count($reply)){
                $chatRecord->delete();
            }else{
                $chatRecord->reply = array_values($reply);
                $chatRecord->save();
            }
        }
        return true;
    }
}