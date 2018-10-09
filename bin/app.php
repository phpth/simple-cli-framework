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
// | Time: 下午 20:52
// +----------------------------------------------------------------------

namespace bin;

use Exception;

class app
{
    const version = '0.10';

    /**
     * 需要固定加载的文件
     * @var array
     */
    protected static $load_file = [
        'common/common.php',
        'vendor/autoload.php',
    ];

    /**
     * 创建框架所需文件夹
     * @var array
     */
    protected static $need_create_dir = [
        TMP_PATH,
    ];

    /**
     * 开始框架处理
     * @param string $mode_class 运行模式的类命名空间
     * @return mixed
     * @throws Exception
     */
    public static function start($mode_class = 'bin\mode\cli')

    {
        app::load ();
        app::createDir();
        app::register ();
        $param = (new $mode_class())->param();
        return app::run($param['construct'],$param['action']);
    }

    /**
     * 带参数并且执行控制器
     * @param array $construct_param
     * @param array $action_param
     * @return mixed
     */
    protected static function run(array $construct_param, array $action_param)
    {
        $class = CONTROLLER;
        return (new $class(...$construct_param))->{ACTION}(...$action_param);
    }

    /**
     * 注册框架自动加载
     */
    protected static function register()
    {
        spl_autoload_register (__NAMESPACE__.'\app::autoLoad',true,true);
    }

    /**
     * 框架自动加载逻辑
     * @param $class
     * @return mixed
     */
    public static function autoLoad($class)
    {
        $class_file = (str_replace ( '\\' , '/' , $class)).'.php';
        $class_path = ROOT_PATH."{$class_file}";
        if(file_exists ( $class_path))
        {
            require $class_path;
        }
        else
        {
            return null;
        }
        return $class;
    }

    /**
     * 加载核心文件
     */
    protected static function load()
    {
        foreach (app::$load_file as $v)
        {
            require ROOT_PATH.$v;
        }
    }

    /**
     * 创建所需文件夹
     */
    protected static function createDir()
    {
        foreach (app::$need_create_dir as $v)
        {
            if(!is_dir($v))
            {
                mkdir($v,0755,true);
            }
        }
    }
}
