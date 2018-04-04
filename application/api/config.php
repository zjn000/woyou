<?php
return [
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------
    'session'                => [
        // SESSION 前缀
        'prefix'         => 'api',
        // 驱动方式 支持redis memcache memcached
        'type'           => 'redis',
        // 是否自动开启 SESSION
        'auto_start'     => true,
        'host'         => '127.0.0.1', // redis主机
        'port'         => 6379, // redis端口
        'password'     => 'W6Y6W6M', // 密码
        'select'       => 0, // 操作库
        'expire'       => 1800, // 有效期(秒)
        'timeout'      => 0, // 超时时间(秒)
        'persistent'   => true, // 是否长连接
        'session_name' => '', // sessionkey前缀
    ]
];