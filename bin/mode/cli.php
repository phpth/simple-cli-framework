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

use Exception;

class cli extends mode
{
    protected $param_flag = [
        '--',
        '-'
    ];

    protected $param_handle ;

    public function __construct ()
    {
        if(!CLI)
        {
            throw new Exception('必须cli模式下运行！');
        }
        $this->param_handle = [
            'help'=>[$this,'help'],
            'pc'=>[$this,'pcPaParam'],
            'pa'=>[$this,'pcPaParam'],
        ];
    }

    /**
     * 获取命令行选项参数
     * @return array|mixed
     * @throws Exception
     */
    public function param()
    {
        global $argv;
        $cli_param = $argv;
        array_shift ( $cli_param );
        $this->router ( empty($cli_param[0])?false:$cli_param[0]);
        for($i=0,$count= count($cli_param);$i<$count;$i++)
        {
            $flag = $this->checkFlag ( $cli_param[$i]);
            if($flag)
            {
                $value_index = strpos ( $cli_param[$i] , '=');
                $flag_len = strlen ( $flag);
                if($value_index!==false)
                {
                    $_GET[substr($cli_param[$i],$flag_len,$value_index)] = substr($cli_param[$i],$value_index);
                }
                else
                {
                    if(empty($cli_param[$i+1]) || $this->checkFlag ($cli_param[$i+1]) )
                    {
                        $_GET[substr($cli_param[$i],$flag_len)] = null ;
                    }
                    else
                    {
                        $_GET[substr($cli_param[$i],$flag_len)] = $cli_param[$i+1] ;
                        $i+=1;
                    }
                }
            }
        }
        $this->cliHandle();
        return ['construct'=>empty($_GET['pc'])?[]:$_GET['pc'], 'action'=>empty($_GET['pa'])?[]:$_GET['pa']];
    }

    /**
     * 检查是否是参数标志
     * @param $value
     * @return bool|mixed
     */
    protected function checkFlag($value)
    {
        foreach($this->param_flag as $flag)
        {
            if(strpos ( $value , $flag) === 0)
            {
                return $flag;
            }
        }
        return false;
    }

    /**
     * 路由 生成常量
     * @param $route_msg
     */
    public function router( $route_msg)
    {
        if ( empty($route_msg) || strpos($route_msg[0] , '-' ) !== false)// 如果第一个是选项则默认为没有指定控制器
        {
            $class = APP_NAME . '/index/index';
        }
        else {
            if ( strpos ( $route_msg , APP_NAME ) === 0 ) {
                $class = $route_msg;
            }
            else {
                $class = APP_NAME . "/$route_msg";
            }
        }
        $c_arr = explode ( '/' , $class ) ;
        $action = array_pop ( $c_arr);
        $class = join('\\',$c_arr);
        define ( 'CONTROLLER' , $class );
        define ( 'ACTION' , $action );
    }

/***********************************cli 参数注册处理函数*******************************************/

    /**
     * 执行注册的参数处理函数
     * @throws Exception
     */
    protected function cliHandle()
    {
        foreach($this->param_handle as $k=>$v)
        {
            if(array_key_exists($k,$_GET))
            {
                if(is_callable ( $v))
                {
                    call_user_func_array ( $v , [$k]);
                }
                else
                {
                    throw new Exception("参数[{$k}]的处理方式[{$v}]无法调用！");
                }
            }
        }
    }

    /**
     * 参数 -pc -pa的处理方法
     * @param $param_name
     */
    public function pcPaParam($param_name)
    {
        $_GET[$param_name] = explode ( ',' , $_GET[$param_name]);
    }

    /**
     *  参数 -help 或者--help 的处理函数
     * @param $param_name
     * @return string
     */
    public function help($param_name)
    {
        $version = \bin\app::version;
        echo <<<eof
        
start:
    [simple cli php, version: v $version ] 
    
    start index/index -pc param1,param2 -pa param1,param2
    其中 index/index 是执行控制器的路径  
    
    -pc 示例：-pc fs,asdf,asdf 向控制器构造方法传递值
    -pa 示例：-pa 1,3,4 向控制器方法调用的方法传递值 ，每个值用逗号分隔，不要有空格
    

eof;
        die();
    }
}