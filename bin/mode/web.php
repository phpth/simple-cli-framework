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
// | Date: 2018/10/3 0003
// +----------------------------------------------------------------------
// | Time: 下午 23:28
// +----------------------------------------------------------------------

namespace bin\mode;

class web extends mode
{
    public function __construct ()
    {
        if(!CLI)
        {
            throw new Exception('必须cli模式下运行！');
        }
    }

    public function param()
    {
        global $argv;
        $cli_param = $argv;
        array_shift ( $cli_param );
        cli::router ( empty($cli_param[1])?false:$cli_param[1]);
        foreach($cli_param as $v)
        {
            if(strpos ( $v , '--') === 0)
            {

            }
            else if ( strpos ( $v , '--') === 0 )
            {

            }
        }
        return ['construct'=>[], 'action'=>[]];
    }

    /**
     *
     * @param $argv_1
     */
    public function router( $argv_1)
    {
        if ( empty($argv_1) || strpos($argv_1[0] , '-' ) !== false)// 如果第一个是选项则默认为没有指定控制器
        {
            $class = APP_NAME . '/index/index';
        }
        else {
            if ( strpos ( $argv_1 , APP_NAME ) === 0 ) {
                $class = $argv_1;
            }
            else {
                $class = APP_NAME . "/$argv_1";
            }
        }
        $c_arr = explode ( '/' , $class ) ;
        $action = array_pop ( $c_arr);
        $class = join('\\',$c_arr);
        define ( 'CONTROLLER' , $class );
        define ( 'ACTION' , $action );
    }

    /**
     *
     */
    protected static function help()
    {

    }
}