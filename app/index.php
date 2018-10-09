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
// | Time: 下午 21:05
// +----------------------------------------------------------------------
namespace app;

use bin\config;
use bin\process;
use bin\tool\log;
use think\Db;

class index
{
    public function __construct ()
    {

    }

    public function index()
    {
        var_dump ( config::get ( 'db'));
        system('echo ;echo "\033[41;37m  hello tiny-cli framework~ \033[0m";echo ;');
    }

    /**
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\db\exception\PDOException
     */
    public function useDb()
    {
        // 数据库配置信息设置（全局有效）
        Db::setConfig(config::get ( 'db'));
        db::execute ( "CREATE TABLE `test_table` (
          `id` int(255) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL DEFAULT '',
          `email` varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY (`id`) USING BTREE,
          KEY `name` (`name`)
        ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;");
        // 进行CURD操作
        Db::table('test_table')
            ->data(['name'=>'thinkphp','email'=>'thinkphp@qq.com'])
            ->insert();
        Db::table('test_table')->find();
        Db::table('test_table')
            ->where('id','>',10)
            ->order('id','desc')
            ->limit(10)
            ->select();
        Db::table('test_table')
            ->where('id',10)
            ->update(['name'=>'test']);
        Db::table('test_table')
            ->where('id',10)
            ->delete();
    }
}