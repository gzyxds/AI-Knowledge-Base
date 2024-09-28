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

use app\common\logic\BaseLogic;
use app\common\model\chat\ChatRecordCategory;
use Exception;

/**
 * 对话分类逻辑类
 */
class ChatCategoryLogic extends BaseLogic
{
    /**
     * @notes 对话分类新增
     * @param int $userId
     * @return bool
     * @author fzr
     */
    public static function add(int $userId): bool
    {
        try {
            ChatRecordCategory::create([
                'name' => '新的会话',
                'user_id' => $userId
            ]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 对话分类编辑
     * @param array $post
     * @return bool|string
     * @author fzr
     */
    public static function edit(array $post): bool|string
    {
        try {
            $model = new ChatRecordCategory();
            $chatRecordCategory = $model
                ->where(['id' => $post['id']])
                ->where(['user_id' => $post['user_id']])
                ->findOrEmpty();

            if ($chatRecordCategory->isEmpty()) {
                return '对话不存在';
            }

            ChatRecordCategory::update([
                'name' => mb_substr($post['name'], 0, 20, "utf-8")
            ], ['id' => $chatRecordCategory['id']]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 对话分类删除
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function del(array $post): bool
    {
        try {
            $model = new ChatRecordCategory();
            $model->where(['user_id'=>$post['user_id'],'id'=>$post['id']])->delete();
            $count = $model->where(['user_id'=>$post['user_id']])->count();
            if( 0 == $count){
                //创建一个默认会话
                ChatRecordCategory::create(['name'=>'新的会话','user_id'=>$post['user_id']]);
            }
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 对话分类清空
     * @param int $userId
     * @return bool
     * @author fzr
     */
    public static function clear(int $userId): bool
    {
        try {
            $model = new ChatRecordCategory();
            $model->where(['user_id' => $userId])->delete();

            // 创建一个默认会话
            ChatRecordCategory::create(['name' => '新的会话', 'user_id' => $userId]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}