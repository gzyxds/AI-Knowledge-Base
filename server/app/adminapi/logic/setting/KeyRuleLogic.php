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

namespace app\adminapi\logic\setting;

use app\common\enum\PoolEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\KeyRule;
use Exception;

/**
 * Key规则逻辑类
 */
class KeyRuleLogic extends BaseLogic
{
    /**
     * @notes 规则详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $model = new KeyRule();
        return $model
            ->withoutField('delete_time')
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * @notes 规则新增
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function add(array $post): bool
    {
        try {
            KeyRule::create([
                'model_id' => $post['model_id'] ?? 0,
                'type'     => $post['type']     ?? PoolEnum::TYPE_CHAT,
                'channel'  => $post['channel']  ?? '',
                'rule'     => $post['rule']     ?? '',
                'prompt'   => $post['prompt']   ?? '',
                'status'   => $post['status']   ?? 0
            ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 规则编辑
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function edit(array $post): bool
    {
        try {
            KeyRule::update([
                'model_id' => $post['model_id'] ?? 0,
                'type'     => $post['type']     ?? PoolEnum::TYPE_CHAT,
                'channel'  => $post['channel']  ?? '',
                'rule'     => $post['rule']     ?? '',
                'prompt'   => $post['prompt']   ?? '',
                'status'   => $post['status']   ?? 0
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 规则删除
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function del(int $id): bool
    {
        try {
            KeyRule::destroy($id);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 规则状态
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function status(int $id): bool
    {
        try {
            $model = new KeyRule();
            $rule = $model->where(['id'=>$id])->findOrEmpty();
            if ($rule->isEmpty()) {
                throw new Exception('数据不存在!');
            }

            if ($rule->status) {
                self::setError('禁用成功');
            } else {
                self::setError('启用成功');
            }

            KeyRule::update([
                'status' => !$rule->status
            ], ['id'=>$id]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}