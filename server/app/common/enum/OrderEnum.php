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

namespace app\common\enum;

class OrderEnum
{
    // 订单状态
    const STATUS_WAIT_PAY      = 0;  //待付款
    const STATUS_FINISH        = 1;  //已完成
    const STATUS_CLOSE         = 2;  //已关闭

    // 退款状态
    const REFUND_STATUS_NO     = 0; // 未退款
    const REFUND_STATUS_OK     = 1; // 已退款

    // 生成状态
    const GEN_WAIT    = 0; // 待生成
    const GEN_ING     = 1; // 生成中
    const GEN_SUCCESS = 2; // 生成成功
    const GEN_FAIL    = 3; // 生成失败

    /**
     * @notes 订单状态描述
     * @param bool|int $status
     * @return array|string
     * @author fzr
     */
    public static function getOrderStatusDesc(bool|int $status = true): array|string
    {
        $desc = [
            self::STATUS_WAIT_PAY      => '待付款',
            self::STATUS_FINISH        => '已完成',
            self::STATUS_CLOSE         => '已关闭'
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '-';
    }

    /**
     * @notes 退款状态
     * @param bool|int $status
     * @return array|string
     */
    public static function getRefundStatusDesc(bool|int $status = true): array|string
    {
        $desc = [
            self::REFUND_STATUS_NO  => '未退款',
            self::REFUND_STATUS_OK  => '已退款',

        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '-';
    }

    /**
     * @notes 生成状态描述
     * @param bool|int $status
     * @return array|string
     * @author fzr
     */
    public static function getGenStatusDesc(bool|int $status = true): array|string
    {
        $desc = [
            self::GEN_WAIT     => '待生成',
            self::GEN_ING      => '生成中',
            self::GEN_SUCCESS  => '生成成功',
            self::GEN_FAIL     => '生成失败'
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '-';
    }
}