<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/29 0029
 * Time: 17:37
 */
return [
    /**
     * 本地数据库
     */
    'type' => 'mysql',
    // 服务器地址
    'hostname' => '127.0.0.1',
    // 数据库名
    'database' => 'eddchat',
    // 用户名
    'username' => 'root',
    // 密码
    'password' => 'root',
    // 端口
    'hostport' => '3306',
    // 数据库表前缀
    'prefix' => 'edd_',
    //调试模式
    //开启:用本地数据库
    //关闭:服务器数据库
    'db_debug'=>false
];