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

/**
 * 支付
 * Class PayEnum
 * @package app\common\enum
 */
class PayEnum
{
    // 支付类型
    const BALANCE_PAY   = 1; // 余额支付
    const WECHAT_PAY    = 2; // 微信支付
    const ALI_PAY       = 3; // 支付宝支付

    // 支付状态
    const UNPAID = 0; // 未支付
    const ISPAID = 1; // 已支付

    // 退款状态
    const REFUND_NOT     = 0; //未退款
    const REFUND_SUCCESS = 1; //已退款

    // 支付场景
    const SCENE_MNP = 1; // 微信小程序
    const SCENE_OA  = 2; // 微信公众号
    const SCENE_H5  = 3; // H5
    const SCENE_PC  = 4; // PC
    const SCENE_APP = 5; // APP

    /**
     * @notes 获取支付类型
     * @param bool|int $value
     * @return string|string[]
     * @author 段誉
     * @date 2023/2/23 15:36
     */
    public static function getPayDesc(bool|int $value = true): array|string
    {
        $data = [
            self::BALANCE_PAY => '余额支付',
            self::WECHAT_PAY  => '微信支付',
            self::ALI_PAY     => '支付宝支付',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }

    /**
     * @notes 支付状态
     * @param bool|int $value
     * @return string|string[]
     * @author 段誉
     * @date 2023/2/23 15:36
     */
    public static function getPayStatusDesc(bool|int $value = true): array|string
    {
        $data = [
            self::UNPAID => '未支付',
            self::ISPAID => '已支付',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }

    /**
     * @notes 支付场景
     * @param bool|int $value
     * @return string|string[]
     * @author 段誉
     * @date 2023/2/23 15:36
     */
    public static function getPaySceneDesc(bool|int $value = true): array|string
    {
        $data = [
            self::SCENE_H5  => 'H5',
            self::SCENE_OA  => '微信公众号',
            self::SCENE_MNP => '微信小程序',
            self::SCENE_APP => 'APP',
            self::SCENE_PC  => 'PC'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }

    /**
     * @notes 退款状态
     * @param bool $value
     * @return string|string[]
     * @author ljj
     */
    public static function getRefundStatusDesc(bool|int $value = true): array|string
    {
        $data = [
            self::REFUND_NOT     => '未退款',
            self::REFUND_SUCCESS => '已退款'
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value] ?? '';
    }
}