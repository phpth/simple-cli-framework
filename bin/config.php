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
// | Time: 下午 21:06
// +----------------------------------------------------------------------

namespace bin;
/**
 * 配置获取
 * Class config
 * @package bin
 */
class config
{
    protected static $data = null ;

    /**
     *
     * @param $key
     * @return null
     */
    public static function get($key)
    {
        if(config::$data === null)
        {
            config::$data = require CONFIG_FILE;
            if(is_file ( config::$data['ext-config-file']))
            {
                config::$data = array_merge( config::$data,parse_ini_file ( config::$data['ext-config-file']));
            }
        }
        if(empty(config::$data))
        {
            return null;
        }
        if($key===true)
        {
            return config::$data;
        }
        $key = trim(trim($key),'.');
        if(empty($key))
        {
            return null ;
        }
        $key = explode ( '.' , $key);
        $data = &config::$data;
        foreach($key as $v)
        {
            if(isset($data[$v]))
            {
                $data =&$data[$v];
            }
            else
            {
                $data = null ;
                break;
            }
        }
        return $data;
    }

    /**
     * 设置配置值
     * @param $key
     * @param $value
     * @return bool
     */
    public static function set($key, $value)
    {
        if(config::$data === null)
        {
            config::$data = require CONFIG_FILE;
        }
        $key =  trim(trim($key),'.');
        if(empty($key))
        {
            return false ;
        }
        $key = explode ( '.' , $key);
        $data = &config::$data;
        foreach($key as $v)
        {
            if(!isset($data[$v]))
            {
                $data[$v] = [];
            }
            $data = &$data[$v];
        }
        $data = $value;
        return true ;
    }
}