<?php
// +----------------------------------------------------------------------
// | phpth|phpy: msg-server
// +----------------------------------------------------------------------
// | Copyright (c) 2018
// +----------------------------------------------------------------------
// | Licensed MIT
// +----------------------------------------------------------------------
// | Author: luajia
// +----------------------------------------------------------------------
// | Date: 2018/9/27 0027
// +----------------------------------------------------------------------
// | Time: 下午 20:56
// +----------------------------------------------------------------------

return [
    'ext-config-file'=>'conf.ini',
    'ttt'=>[1],
    'cc'=>[],


    'db'=>[
        // 数据库类型
        'type'        => 'mysql',
        // 服务器地址
        'hostname'    => '127.0.0.1',
        // 数据库名
        'database'    => 'test',
        // 数据库用户名
        'username'    => 'root',
        // 数据库密码
        'password'    => '',
        // 数据库连接端口
        'hostport'    => '3306',
        // 数据库连接参数
        'params'      => [],
        // 数据库编码默认采用utf8
        'charset'     => 'utf8',
        // 开启断线重连
        'break_reconnect' => false,
        // 数据库表前缀
        'prefix'      => '',
    ],
];
