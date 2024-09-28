<?php

use app\common\enum\VoiceEnum;
use think\facade\Request;

return [
    // 系统版本号
    'version' => '4.1.1',

    // 官网
    'website' => [
        'name'        => env('project.web_name', 'chatmoney'),             // 网站名称
        'url'         => env('project.web_url', Request::domain()),               // 网站地址
        'login_image' => 'resource/image/adminapi/default/login_image.png',             // 登录封面
        'web_logo'    => 'resource/image/adminapi/default/web_logo.jpg',                // 网站logo
        'web_favicon' => 'resource/image/adminapi/default/web_favicon.ico',             // 网站图标
        'pc_logo'     => 'resource/image/adminapi/default/pc_logo.png',                 // PC端Logo
        'pc_ico'      => 'resource/image/adminapi/default/web_favicon.ico',             // PC端图标
        'pc_title'    => 'chatmoney'                                                     // PC网站标题
    ],

    // 后台登录
    'admin_login' => [
        'login_restrictions'   => 1,  // 管理后台登录限制 0-不限制 1-需要限制
        'password_error_times' => 5,  // 限制密码错误次数
        'limit_login_time'     => 30  // 限制禁止多少分钟不能登录
    ],

    // 唯一标识,密码盐、路径加密等
    'unique_identification' => env('project.unique_identification', 'ChatWork'),

    // 后台管理员token(登录令牌)配置
    'admin_token' => [
        'expire_duration'    => 3600 * 8, // 管理后台token过期时长(单位秒）
        'be_expire_duration' => 3600,  // 管理后台token临时过期前时长，自动续期
    ],

    // 商城用户token(登录令牌)配置
    'user_token' => [
        'expire_duration'    => 3600 * 24 * 7, // 用户token过期时长(单位秒）
        'be_expire_duration' => 3600      // 用户token临时过期前时长，自动续期
    ],

    // 列表页
    'lists' => [
        'page_size_max' => 25000, // 列表页查询数量限制（列表页每页数量、导出每页数量）
        'page_size'     => 25     // 默认每页数量
    ],

    // 各种默认图片
    'default_image' => [
        'chat_logo'        => 'resource/image/adminapi/default/chat_logo.png',        // 对话图标
        'chat_example'     => 'resource/image/adminapi/default/chat_example.png',     // 对话示例
        'model_example'     => 'resource/image/adminapi/default/model_example.png',   // 模型示例

        'robot_icon'       => 'resource/image/adminapi/default/robot_icon.gif',       // 机器人默认图标
        'robot_chat'       => 'resource/image/adminapi/default/robot_chat.png',       // 机器人对话图片

        'admin_avatar'     => 'resource/image/adminapi/default/avatar.png',           // 后台头像
        'user_avatar'      => 'resource/image/adminapi/default/default_avatar.png',   // 用户头像
        'qq_group'         => 'resource/image/adminapi/default/qq_group.png',         // qq群
        'customer_service' => 'resource/image/adminapi/default/customer.jpg',         // 客服

        'menu_type'       => 'resource/image/adminapi/default/menu_type.png',       // 首页快捷菜单-机器人应用
        'menu_make'       => 'resource/image/adminapi/default/menu_make.png',       // 首页快捷菜单-知识库管理
        'menu_order'      => 'resource/image/adminapi/default/menu_order.png',      // 首页快捷菜单-订单管理
        'menu_user'       => 'resource/image/adminapi/default/menu_user.png',       // 首页快捷菜单-用户管理
        'menu_finance'    => 'resource/image/adminapi/default/menu_finance.png',    // 首页快捷菜单-财务中心
        'menu_keys'       => 'resource/image/adminapi/default/menu_keys.png',       // 首页快捷菜单-Key池管理
        'menu_notice'     => 'resource/image/adminapi/default/menu_notice.png',     // 首页快捷菜单-Key池管理
        'menu_website'    => 'resource/image/adminapi/default/menu_website.png',    // 首页快捷菜单-网站设置

        'wechat'          => 'resource/image/api/default/wechat.png',// 微信图标
        'wechat_qrcode'   => 'resource/image/api/default/wechat_qrcode.png',// 微信二维码图标
        'ali'             => 'resource/image/api/default/ali.png',// 支付宝图标
        'ali_qrcode'      => 'resource/image/api/default/ali_qrcode.png',// 支付宝二维码图标

        'draw_censor_fail' => 'resource/image/draw/draw_censor_fail.png', // 绘画违规默认显示图片
    ],

    // 文件上传限制 (图片)
    'file_image' => ['jpg', 'png', 'gif', 'jpeg', 'webp'],

    // 文件上传限制 (视频)
    'file_video' => ['wmv', 'avi', 'mpg', 'mpeg', '3gp', 'mov', 'mp4', 'flv', 'f4v', 'rmvb', 'mkv'],

    // 文件上传限制 (文件)
    'file_file' => ['txt', 'pdf', 'ppt', 'pptx', 'doc', 'docx', 'xlx', 'xlsx', 'md', 'rtf', 'csv', 'json'],

    // 文件上传限制 (音频)
    'file_audio' => ['mp3', 'mp4', 'wav', 'acc', 'f;ac', 'ape'],

    // 登录设置
    'login' => [
        'register_way'        => ['1', '2'],  // 注册方式：1-手机号注册；2-邮箱注册
        'login_way'           => ['1', '2'],  // 登录方式：1-手机号登录；2-邮箱登录
        'default_login_way'   => 1,           // 默认登录方式: 1-手机号登录; 2-邮箱登录
        'register_sms_verify' => 0,           // 注册验证码 0-关闭 1-开启
        'is_agreement'        => 1            // 政策协议 0-关闭 1-开启
    ],

    // 注册奖励
    'register_rewards' => [
        'reward_chat'  => 10000, // 赠送对话tokens
        'reward_robot' => 2      // 赠送机器人数量
    ],

    // 语音播报配置
    'voice_output' => [
        'is_open'  => 0, // 默认关闭
        'channel'  => VoiceEnum::KDXF,
        'save_dir' => 'uploads/voice/output',
    ],

    // 语音对话配置
    'voice_input' => [
        'is_open'   => 0, //默认关闭
        'channel'   => VoiceEnum::KDXF,
        'save_dir'  => 'uploads/voice/input'
    ],
    //思维导图配置
    'mindmap_config'   => [
        //思维导图示例开关：1-开启；0-关闭；
        'is_example'   => 1,
        'example_content' => ['# AI系统'.PHP_EOL.'## 基础功能'.PHP_EOL.'- 支持AI对话聊天'.PHP_EOL.'- 支持AI智能写作'.PHP_EOL.'- 支持AI绘画、绘画广场'.PHP_EOL.'- 支持星火等认知大模型'.PHP_EOL.'- 支持Midjourney'.PHP_EOL.'- 支持思维导图生成'.PHP_EOL.'- 更多功能等你探索......'.PHP_EOL.'## 更多内容'.PHP_EOL.'-  输入您想要生成的内容'.PHP_EOL.'- 点击生成即可'.PHP_EOL.'## 联系我们'.PHP_EOL.'- 微信群1'.PHP_EOL.'- 微信群2'.PHP_EOL.'- 联系客服'],
        'cue_word' => '请按我接下来说的主题帮我制作一份思维导图，列出主分支内容和子分支内容，你需按以下格式返回数据："
# {标题}
## {子标题}
- {内容} 
..."，不要返回其他提示信息或解释，我的主题是：{prompt}'
    ],
    //分享奖励
    'share_award'   => [
        'is_open'           => 0,
        'one_award'         => 1,
        'day_num'           => 5,
    ],
    //邀请奖励
    'invite_award'   => [
        'is_open'           => 0,
        'one_award'         => 1,
        'day_num'           => 5,
    ],
    //签到奖励
    'sign_award'   => [
        'is_open'           => 0,
        'one_award'         => 1,
    ],
    //绘画分享奖励
    'draw_award'   => [
        'is_open'           => 0,
        'one_award'         => 1,
        'day_num'           => 1,
        'auto_audit'        => 1,
        'is_show_user'      => 1,
    ],
    //音乐分享奖励
    'music_award'   => [
        'is_open'           => 0,
        'one_award'         => 1,
        'day_num'           => 1,
        'auto_audit'        => 1,
        'is_show_user'      => 1,

    ],
    //视频分享奖励
    'video_award'   => [
        'is_open'           => 0,
        'one_award'         => 1,
        'day_num'           => 1,
        'auto_audit'        => 1,
        'is_show_user'      => 1,
    ],
    //机器人分享奖励
    'robot_award'   => [
        'is_open'           => 0,
        'one_award'         => 1,
        'day_num'           => 5,
        'auto_audit'        => 1,
        'is_show_user'      => 1,
    ],
];
