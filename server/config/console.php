<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
use app\common\command\WechatMerchantTransfer;

return [
    // 指令定义
    'commands' => [
        // 定时任务
        'crontab' => 'app\common\command\Crontab',
        // 内容审核
        'content_censor' => 'app\common\command\ContentCensor',
        // 退款查询
        'query_refund' => 'app\common\command\QueryRefund',
        // 修改密码
        'password' => 'app\common\command\Password',
        // 音乐查询处理
        'query_music' => 'app\common\command\QueryMusic',
        // 更新脚本
        'update_data' => 'app\common\command\UpdateData',
        // 绘画失败
        'draw_fail' => 'app\common\command\DrawFail',
        // 视频查询处理
        'query_video' => 'app\common\command\QueryVideo',
        // 商家转账到零钱查询
        'wechat_merchant_transfer' => WechatMerchantTransfer::class,
    ],
];
