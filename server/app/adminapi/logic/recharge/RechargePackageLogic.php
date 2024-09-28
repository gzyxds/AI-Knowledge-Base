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

namespace app\adminapi\logic\recharge;

use app\common\logic\BaseLogic;
use app\common\model\recharge\RechargePackage;
use app\common\service\ConfigService;
use Exception;

/**
 * 充值套餐逻辑类
 */
class RechargePackageLogic extends BaseLogic
{
    /**
     * @notes 套餐详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $model = new RechargePackage();
        return $model
            ->withoutField('delete_time')
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * @notes 套餐新增
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function add(array $post): bool
    {
        try {
            RechargePackage::create([
                'name'                => $post['name'],
                'remarks'             => $post['remarks']             ?? '',
                'sell_price'          => $post['sell_price']          ?? 0,
                'line_price'          => $post['line_price']          ?? 0,
                'chat_balance'        => $post['chat_balance']        ?? 0,
                'robot_number'        => $post['robot_number']        ?? 0,
                'video_duration'      => $post['video_duration']      ?? 0,
                'give_chat_balance'   => $post['give_chat_balance']   ?? 0,
                'give_robot_number'   => $post['give_robot_number']   ?? 0,
                'give_video_duration' => $post['give_video_duration'] ?? 0,
                'tags'                => $post['tags']                ?? '',
                'sort'                => $post['sort']                ?? 0,
                'status'              => $post['status']              ?? 0,
                'is_give'             => $post['is_give']             ?? 0,
                'is_recommend'        => $post['is_recommend']        ?? 0,
                'create_time'         => time(),
                'update_time'         => time()
            ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 套餐编辑
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function edit(array $post): bool
    {
        try {
            RechargePackage::update([
                'name'                => $post['name'],
                'remarks'             => $post['remarks']               ??'',
                'sell_price'          => $post['sell_price']            ?? 0,
                'line_price'          => $post['line_price']            ?? 0,
                'chat_balance'        => $post['chat_balance']          ?? 0,
                'robot_number'        => $post['robot_number']          ?? 0,
                'video_duration'      => $post['video_duration']        ?? 0,
                'give_chat_balance'   => $post['give_chat_balance']     ?? 0,
                'give_robot_number'   => $post['give_robot_number']     ?? 0,
                'give_kb_number'      => $post['give_kb_number']        ?? 0,
                'give_video_duration' => $post['give_video_duration']   ?? 0,
                'tags'                => $post['tags']                  ?? '',
                'sort'                => $post['sort']                  ?? 0,
                'status'              => $post['status']                ?? 0,
                'is_give'             => $post['is_give']               ?? 0,
                'is_recommend'        => $post['is_recommend']          ?? 0,
                'update_time'         => time()
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 套餐删除
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function del(int $id): bool
    {
        try {
            RechargePackage::destroy($id);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 套餐状态
     * @param int $id
     * @param string $field
     * @param int $value
     * @return bool
     */
    public static function status(int $id, string $field, int $value): bool
    {
        try {
            if ($field === 'is_recommend' && $value) {
                (new RechargePackage())
                    ->where('id', '>', 0)
                    ->update([
                        'is_recommend' => 0
                    ]);
            }

            RechargePackage::update([
                $field => $value
            ], ['id'=>$id]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 套餐排序
     * @param int $id
     * @param int $value
     * @return bool
     */
    public static function sort(int $id, int $value): bool
    {
        try {
            RechargePackage::update([
                'sort'        => $value,
                'update_time' => time()
            ], ['id'=>$id]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 充值配置参数
     * @return array
     * @author fzr
     */
    public static function getConfig(): array
    {
        $rechargeStatus = ConfigService::get('recharge', 'status', 0);
        return [
            'rechargeStatus' => $rechargeStatus
        ]??[];
    }

    /**
     * @notes 充值配置保存
     * @param array $post
     * @author fzr
     */
    public static function setConfig(array $post)
    {
        ConfigService::set('recharge', 'status', $post['rechargeStatus']??0);
    }
}