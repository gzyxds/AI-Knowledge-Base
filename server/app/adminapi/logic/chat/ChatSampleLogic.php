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

namespace app\adminapi\logic\chat;

use app\common\logic\BaseLogic;
use app\common\model\chat\ChatSample;
use Exception;

/**
 * 示例逻辑类
 */
class ChatSampleLogic extends BaseLogic
{
    /**
     * @notes 示例详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        return (new ChatSample())
            ->withoutField('update_time,delete_time')
            ->with(['category' => function ($query) {
                $query->bind(['name']);
            }])
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * @notes 示例新增
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function add(array $post): bool
    {
        try {
            ChatSample::create([
                'category_id' => $post['category_id']??0,
                'sort'        => $post['sort']??0,
                'content'     => $post['content']??'',
                'status'      => $post['status']??0,
                'create_time' => time(),
                'update_time' => time()
            ]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 示例编辑
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function edit(array $post): bool
    {
        try {
            ChatSample::update([
                'category_id' => $post['category_id']??0,
                'sort'        => $post['sort']??0,
                'content'     => $post['content']??'',
                'status'      => $post['status']??0,
                'update_time' => time()
            ], ['id'=>intval($post['id'])]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 示例删除
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function del(int $id): bool
    {
        try {
            ChatSample::destroy($id);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 修改状态
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function status(int $id): bool
    {
        try {
            $questionCategory = (new ChatSample())->where(['id' => $id])->findOrEmpty();
            if ($questionCategory->isEmpty()) {
                return true;
            }

            $questionCategory->status = $questionCategory->status ? 0 : 1;
            $questionCategory->save();
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}