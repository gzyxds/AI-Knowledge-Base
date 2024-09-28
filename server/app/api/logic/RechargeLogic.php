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

use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\recharge\RechargeOrder;
use app\common\model\recharge\RechargePackage;
use app\common\service\ConfigService;
use Exception;

/**
 * 充值逻辑层
 */
class RechargeLogic extends BaseLogic
{
    /**
     * @notes 充值下单
     * @param array $params
     * @return bool|array
     * @author fzr
     */
    public static function place(array $params): bool|array
    {
        try {
            $rechargeStatus = ConfigService::get('recharge', 'status', 0);
            if (!$rechargeStatus) {
                throw new Exception('充值功能已被关闭!');
            }

            $model = new RechargePackage();
            $package = $model
                ->withoutField('delete_time')
                ->where(['id'=>intval($params['package_id'])])
                ->findOrEmpty()
                ->toArray();

            if (!$package) {
                throw new Exception('套餐不存在了!');
            }

            if (!$package['status']) {
                throw new Exception('套餐已下架了!');
            }

            $order = RechargeOrder::create([
                'order_sn'       => generate_sn(RechargeOrder::class, 'order_sn'),
                'pay_status'     => PayEnum::UNPAID,
                'user_id'        => $params['user_id'],
                'package_id'     => $params['package_id'],
                'order_terminal' => $params['terminal'],
                'order_amount'   => $package['sell_price'],
                'chat_balance'   => $package['chat_balance']    + $package['give_chat_balance'],
                'robot_number'   => $package['robot_number']   + $package['give_robot_number'],
                'video_duration' => $package['video_duration'] + $package['give_video_duration'],
                'snapshot'       => json_encode($package, JSON_UNESCAPED_UNICODE)
            ]);

            return [
                'order_id' => intval($order['id']),
                'from'     => 'recharge'
            ];
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}