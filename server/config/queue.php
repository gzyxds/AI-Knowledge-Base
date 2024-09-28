<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

use think\facade\Env;

return [
    'default' => 'redis',
    'connections' => [
        'redis' => [
            'type'       => 'redis',
            'host'       => Env::get('queue.host', 'likeshop-redis'), // redis 主机ip
            'port'       => Env::get('queue.port', '6379'),           // redis 端口
            'password'   => Env::get('queue.password', ''),           // redis 密码
            'select'     => 0,
            'timeout'    => 0,
            'persistent' => false,
            'queue'      => Env::get('queue.name', 'default'),
        ],
    ],
    'failed' => [
        'type' => 'none',
        'table' => 'failed_jobs',
    ],
];
