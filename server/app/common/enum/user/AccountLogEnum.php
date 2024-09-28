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

namespace app\common\enum\user;

use app\common\service\ConfigService;

/**
 * 用户账户流水变动表枚举
 */
class AccountLogEnum
{
    /**
     * 动作
     */
    const INC = 1; // 增加
    const DEC = 2; // 减少

    /**
     * 变动对象
     */
    const UM    = 1;  // 钱包余额
    const ROBOT = 2;  // 机器人数
    const VIDEO = 3;  // 数字人数
    const MONEY = 4;  // 佣金


    /**
     * 对话类型
     */
    const UM_DEC_ADMIN         = 100;  // 管理员减少余额
    const UM_DEC_REFUND        = 101;  // 充值退款减余额
    const UM_DEC_CHAT          = 102;  // AI对话消耗余额
    const UM_DEC_ROBOT_CHAT    = 103;  // 应用对话消耗余额
    const UM_DEC_KB_TEACH      = 104;  // 数据训练消耗余额
    const UM_DEC_KB_QA         = 105;  // 问答拆分消耗余额
    const UM_DEC_MEMBER_REFUND = 106;  // 会员退款退回电力值
    const UM_DEC_MUSIC         = 107;  // AI音乐消耗余额
    const UM_DEC_MUSIC_IMAGINE = 108;  // AI音乐联想消耗余额
    const UM_DEC_DRAW          = 109;  // AI绘画消耗余额
    const UM_DEC_VIDEO         = 110;  // AI视频消耗余额
    const UM_DEC_SEARCH        = 111;  // AI搜索消耗余额


    const UM_INC_ADMIN          = 200;  // 管理员增加对话数
    const UM_INC_RECHARGE       = 201;  // 充值增加对话数量
    const UM_INC_REGISTER       = 202;  // 注册赠送对话数量

    const UM_INC_MEMBER         = 203;  // 开通会员赠送电力值
    const UM_INC_MUSIC_FAIL     = 204;  // AI音乐失败退回余额
    const UM_INC_DRAW_FAIL      = 205;  // AI音乐失败退回余额
    const UM_INC_VIDEO_FAIL     = 206;  // AI视频失败退回余额

    const UM_INC_INVITE         = 207;  // 邀请奖励
    const UM_INC_CARDCODE_GIVE  = 208;  //卡密兑换赠送电力值
    const UM_INC_SHARE          = 209;  //分享赠送电力值
    const UM_INC_SIGN           = 210;  //签到赠送电力值
    const UM_INC_DRAW_SHARE     = 211;  //绘画分享赠送电力值
    const UM_INC_MUSIC_SHARE    = 212;  //音乐分享赠送电力值
    const UM_INC_VIDEO_SHARE    = 213;  //视频分享赠送电力值
    const UM_INC_ROBTO_SHARE    = 214;  //机器人分享赠送电力值
    const UM_INC_SEARCH         = 215;  //AI搜索失败退回余额


    /**
     *  机器人类型
     */
    const ROBOT_DEC_ADMIN         = 300;  // 系统扣减机器人
    const ROBOT_DEC_REFUND        = 301;  // 退款扣减机器人
    const ROBOT_DEC_CREATE        = 302;  // 创建消耗机器人
    const ROBOT_DEC_MEMBER_REFUND = 303;  // 会员退款退回机器人

    const ROBOT_INC_ADMIN    = 401;  // 系统添加机器人
    const ROBOT_INC_REGISTER = 402;  // 注册赠送机器人
    const ROBOT_INC_RECHARGE = 403;  // 充值增加机器人
    const ROBOT_INC_DELETE   = 404;  // 删除退回机器人
    const ROBOT_INC_MEMBER   = 405;  // 开通会员赠送机器人

    /**
     * 可提现佣金减少类型
     */
    const MONEY_DEC_WITHDRAW        = 500;

    /**
     * 可提现佣金增加类型
     */
    const MONEY_INC_WITHDRAW_FAIL   = 600;
    const MONEY_INC_DISTRIBUTION    = 601;


    /**
     * 视频合成类型
     */
    const VIDEO_DEC_ADMIN    = 700;   // 管理减少视频合成时长
    const VIDEO_DEC_REFUND   = 701;   // 充值退款减少合成时长
    const VIDEO_DEC_GENERATE = 702;   // 视频合成减少合成时长
    const VIDEO_INC_ADMIN    = 800;   // 管理增加视频合成时长
    const VIDEO_INC_RECHARGE = 801;   // 用户充值视频合成时长
    const VIDEO_INC_REGISTER = 802;   // 注册赠送视频合成时长

    /**
     * 对话余额（减少类型汇总）
     */
    const UM_DEC = [
        self::UM_DEC_ADMIN,
        self::UM_DEC_CHAT,
        self::UM_DEC_DRAW,
    ];


    /**
     * 对话余额（增加类型汇总）
     */
    const UM_INC = [
        self::UM_INC_ADMIN,
        self::UM_INC_RECHARGE,
        self::UM_INC_REGISTER,
        self::UM_INC_DRAW_FAIL,
        self::UM_INC_CARDCODE_GIVE,
    ];




    /**
     * @notes 获取变动描述
     * @param $changeType
     * @param bool|int $flag
     * @return array|string
     * @author fzr
     */
    public static function getChangeTypeDesc($changeType, bool|int $flag = false): array|string
    {
        $unit = ConfigService::get('chat', 'price_unit', '电力值');

        $desc = [
            self::UM_DEC_ADMIN         => '管理员减少' . $unit,
            self::UM_DEC_REFUND        => '充值退款退' . $unit,
            self::UM_DEC_CHAT          => 'AI对话消耗' . $unit,
            self::UM_DEC_ROBOT_CHAT    => '知识库对话消耗' . $unit,
            self::UM_DEC_KB_TEACH      => '数据训练消耗' . $unit,
            self::UM_DEC_KB_QA         => '问答拆分消耗' . $unit,
            self::UM_DEC_MEMBER_REFUND => '开通会员退还' . $unit,
            self::UM_DEC_MUSIC         => 'AI音乐消耗' . $unit,
            self::UM_DEC_MUSIC_IMAGINE => 'AI音乐联想消耗' . $unit,
            self::UM_DEC_DRAW          => 'AI绘画消耗' . $unit,
            self::UM_DEC_VIDEO         => 'AI视频消耗' . $unit,
            self::UM_DEC_SEARCH        => 'AI搜索消耗' . $unit,
            self::UM_INC_ADMIN         => '管理员增加' . $unit,
            self::UM_INC_RECHARGE      => '充值增加' . $unit,
            self::UM_INC_REGISTER      => '注册赠送' . $unit,
            self::UM_INC_MEMBER        => '开通会员赠送' . $unit,
            self::UM_INC_MUSIC_FAIL    => 'AI音乐失败退回' . $unit,
            self::UM_INC_DRAW_FAIL     => 'AI绘画失败退回' . $unit,
            self::UM_INC_VIDEO_FAIL    => 'AI视频失败退回' . $unit,
            self::UM_INC_CARDCODE_GIVE => '卡密充值' . $unit,
            self::UM_INC_SHARE         => '分享赠送' . $unit,
            self::UM_INC_INVITE        => '邀请赠送' . $unit,
            self::UM_INC_SIGN          => '签到赠送' . $unit,
            self::UM_INC_DRAW_SHARE    => '绘画分享赠送' . $unit,
            self::UM_INC_MUSIC_SHARE    => '音乐分享赠送' . $unit,
            self::UM_INC_VIDEO_SHARE    => '视频分享赠送' . $unit,
            self::UM_INC_ROBTO_SHARE    => '智能体分享赠送' . $unit,
            self::UM_INC_SEARCH         => 'AI搜索失败退回' . $unit,

            self::ROBOT_DEC_ADMIN         => '系统扣减智能体',
            self::ROBOT_DEC_REFUND        => '退款扣减智能体',
            self::ROBOT_DEC_CREATE        => '创建消耗智能体',
            self::ROBOT_DEC_MEMBER_REFUND => '删除退回智能体',
            self::ROBOT_INC_ADMIN         => '系统添加智能体',
            self::ROBOT_INC_REGISTER      => '注册赠送智能体',
            self::ROBOT_INC_RECHARGE      => '充值增加智能体',
            self::ROBOT_INC_DELETE        => '删除退回智能体',
            self::ROBOT_INC_MEMBER        => '开通会员赠送智能体',

            self::MONEY_DEC_WITHDRAW      => '提现扣减佣金',
            self::MONEY_INC_WITHDRAW_FAIL => '提现失败退回佣金',
            self::MONEY_INC_DISTRIBUTION  => '分销收益',

            self::VIDEO_DEC_ADMIN    => '管理减少视频合成时长',
            self::VIDEO_DEC_REFUND   => '充值退款减少合成时长',
            self::VIDEO_DEC_GENERATE => '视频合成减少合成时长',
            self::VIDEO_INC_ADMIN    => '管理增加视频合成时长',
            self::VIDEO_INC_RECHARGE => '用户充值视频合成时长',
            self::VIDEO_INC_REGISTER => '注册赠送视频合成时长',


        ];

        if ($flag) {
            return $desc;
        }

        return $desc[$changeType] ?? '';
    }

    /**
     * @notes 取变动对象
     * @param $changeType
     * @return int
     * @author fzr
     */
    public static function getChangeObject($changeType): int
    {
        // 对话类型
        if ($changeType >= 100 && $changeType < 300) {
            return self::UM;
        }

        // 机器人类型
        if ($changeType >= 300 && $changeType < 500) {
            return self::ROBOT;
        }

        // 视频合成类型
        if ($changeType >= 700 && $changeType < 900) {
            return self::VIDEO;
        }

        // 分销合成类型
        if ($changeType >= 500 && $changeType < 700) {
            return self::MONEY;
        }

        return 0;
    }


    /**
     * 可提现佣金（减少类型汇总）
     */
    const MONEY_DEC = [
        self::MONEY_DEC_WITHDRAW,
    ];


    /**
     * 可提现佣金（增加类型汇总）
     */
    const MONEY_INC = [
        self::MONEY_INC_WITHDRAW_FAIL,
        self::MONEY_INC_DISTRIBUTION,
    ];


    /**
     * @notes 取变动类型数组
     * @param int $type
     * @param string $scene
     * @return array
     * @author fzr
     */
    public static function getChangeTypeCodes(int $type, string $scene = 'all'): array
    {
        $dec = [];
        $inc = [];
        switch ($type) {
            case self::UM:
                $dec = [
                    self::UM_DEC_ADMIN,
                    self::UM_DEC_REFUND,
                    self::UM_DEC_CHAT,
                    self::UM_DEC_ROBOT_CHAT,
                    self::UM_DEC_KB_TEACH,
                    self::UM_DEC_KB_QA,
                    self::UM_DEC_MUSIC,
                    self::UM_DEC_MUSIC_IMAGINE,
                    self::UM_DEC_DRAW,
                    self::UM_DEC_VIDEO,
                    self::UM_DEC_SEARCH
                ];
                $inc = [
                    self::UM_INC_ADMIN,
                    self::UM_INC_RECHARGE,
                    self::UM_INC_REGISTER,
                    self::UM_INC_MEMBER,
                    self::UM_INC_MUSIC_FAIL,
                    self::UM_INC_DRAW_FAIL,
                    self::UM_INC_VIDEO_FAIL,
                    self::UM_INC_CARDCODE_GIVE,
                    self::UM_INC_SHARE,
                    self::UM_INC_INVITE,
                    self::UM_INC_SIGN,
                    self::UM_INC_DRAW_SHARE,
                    self::UM_INC_MUSIC_SHARE,
                    self::UM_INC_VIDEO_SHARE,
                    self::UM_INC_ROBTO_SHARE,
                    self::UM_INC_SEARCH,
                ];
                break;
            case self::ROBOT:
                $dec = [
                    self::ROBOT_DEC_ADMIN,
                    self::ROBOT_DEC_REFUND,
                    self::ROBOT_DEC_CREATE,
                    self::ROBOT_DEC_MEMBER_REFUND
                ];
                $inc = [
                    self::ROBOT_INC_ADMIN,
                    self::ROBOT_INC_REGISTER,
                    self::ROBOT_INC_RECHARGE,
                    self::ROBOT_INC_DELETE,
                    self::ROBOT_INC_MEMBER
                ];
                break;
            case self::VIDEO:
                $dec = [
                    self::VIDEO_DEC_ADMIN,
                    self::VIDEO_DEC_REFUND,
                    self::VIDEO_DEC_GENERATE
                ];
                $inc = [
                    self::VIDEO_INC_ADMIN,
                    self::VIDEO_INC_RECHARGE,
                    self::VIDEO_INC_REGISTER
                ];
                break;
            case self::MONEY:
                $dec = [
                    self::MONEY_DEC_WITHDRAW,
                ];
                $inc = [
                    self::MONEY_INC_WITHDRAW_FAIL,
                    self::MONEY_INC_DISTRIBUTION,
                ];
                break;
        }

        if ($scene == 'all') {
            return array_merge($dec, $inc);
        } elseif ($scene == 'dec') {
            return $dec;
        } elseif ($scene == 'inc') {
            return $inc;
        }

        return [];
    }



    /**
     * @notes 获取可提现佣金变动类型
     * @return int[]
     * @author 段誉
     * @date 2023/2/23 10:08
     */
    public static function getUserMoneyChangeType() : array
    {
        return array_merge(self::MONEY_DEC, self::MONEY_INC);
    }

    /**
     * @notes 获取对话余额类型描述
     * @return string|string[]
     * @author 段誉
     * @date 2023/2/23 10:08
     */
    public static function getBalanceChangeTypeDesc()
    {
        $UMChangeType = self::getBalanceChangeType();
        $changeTypeDesc = self::getChangeTypeDesc('', true);
        return array_filter($changeTypeDesc, function ($key) use ($UMChangeType) {
            return in_array($key, $UMChangeType);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @notes 获取可提现佣金类型描述
     * @return string|string[]
     * @author 段誉
     * @date 2023/2/23 10:08
     */
    public static function getUserMoneyChangeTypeDesc()
    {
        $MONEYChangeType = self::getUserMoneyChangeType();
        $changeTypeDesc = self::getChangeTypeDesc('', true);
        return array_filter($changeTypeDesc, function ($key) use ($MONEYChangeType) {
            return in_array($key, $MONEYChangeType);
        }, ARRAY_FILTER_USE_KEY);
    }

//    /**
//     * @notes 获取对话余额类型描述
//     * @return string|string[]
//     * @author 段誉
//     * @date 2023/2/23 10:08
//     */
//    public static function getBalanceDrawChangeTypeDesc()
//    {
//        $UMChangeType = self::getBalanceDrawChangeType();
//        $changeTypeDesc = self::getChangeTypeDesc('', true);
//        return array_filter($changeTypeDesc, function ($key) use ($UMChangeType) {
//            return in_array($key, $UMChangeType);
//        }, ARRAY_FILTER_USE_KEY);
//    }

    /**
     * @notes 获取对话余额变动类型
     * @return int[]
     * @author 段誉
     * @date 2023/2/23 10:08
     */
    public static function getBalanceChangeType() : array
    {
        return array_merge(self::UM_DEC, self::UM_INC);
    }

//    /**
//     * @notes 获取对话余额变动类型
//     * @return int[]
//     * @author 段誉
//     * @date 2023/2/23 10:08
//     */
//    public static function getBalanceDrawChangeType() : array
//    {
//        return array_merge(self::DRAW_DEC, self::DRAW_INC);
//    }


}