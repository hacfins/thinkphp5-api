<?php
// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------


return [
    // 默认缓存驱动
    'default' => yaconf('cache.driver', 'file'),

    // 缓存连接方式配置
    'stores'  => [
        'redis_cache' => [
            'type'       => 'redis',

            'host'       => yaconf('redis.host'),
            'port'       => yaconf('redis.port'),
            'password'   => yaconf('redis.password'),
            'persistent' => true,

            // 缓存有效期 0表示永久缓存
            'expire'     => 7200,
            // 缓存前缀
            'prefix'     => 'a_',
        ],

        'redis_session' => [
            'type'       => 'redis',

            'host'       => yaconf('redis_session.host'),
            'port'       => yaconf('redis_session.port'),
            'password'   => yaconf('redis_session.password'),
            'persistent' => true,

            // 缓存有效期 0表示永久缓存
            'expire'     => 7200,
            // 缓存前缀
            'prefix'     => 'a_s_',
        ],

        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => 'a_',
            // 缓存有效期 0表示永久缓存
            'expire'     => 7200,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // 更多的缓存连接
    ],
];
