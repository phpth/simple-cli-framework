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
// | Date: 2018/10/5 0005
// +----------------------------------------------------------------------
// | Time: 下午 13:58
// +----------------------------------------------------------------------

namespace bin;

use Exception;

class process
{
    protected $process_list = [];

    protected $cmd_list_info = [];

    protected $stop_process_list = [];

    protected $log_handle ;

    /**
     * 要运行的cmd数组信息
     * process constructor.
     * @param array $cmd_arr 格式 [ 0=>['cmd'=>"php -r 'var_dump($_SERVER)'",'out'=>true ] ]
     * @param string $log screen 代表输出到终端 ，文件路径代表写入到文件
     * @throws Exception
     */
    public function __construct ( array $cmd_arr, $log='screen' )
    {
        if(strtolower ( $log) === 'screen')
        {
            $this->log_handle = STDOUT;
        }
        else
        {
            if(!is_dir(dirname ( $log)))
            {
               if(!mkdir(dirname ( $log), 0777, true))
               {
                   throw new Exception('创建日志文件失败！');
               }
            }
            $this->log_handle = fopen ( $log , 'a+');
        }
        foreach ( $cmd_arr as $k => $v ) {
            if ( isset( $v[ 'cmd' ] ) ) {
                $out = [ 'file' , $v[ 'out' ] , 'a' ];
                if(!is_dir(dirname ( $v['out'])))
                {
                    if(!mkdir(dirname ( $v['out']), 0777, true))
                    {
                        throw new Exception("为文件：{$v['out']} 创建文件夹失败！");
                    }
                }
                $this -> cmd_list_info[ $k ] = [ 'des' => [ 0 => [ 'pipe' , 'r' ] , 1 => $out , 2 => $out ] , 'cmd' => $v[ 'cmd' ] , 'resource' => null , 'pipe' => null ];
            }
            else {
                throw new Exception( "下标{$k}元素必须存在cmd键值，代表要执行的命令！" );
            }
        }
    }

    /**
     * 执行命令进程
     * @param int $run_cmd_times 0: 代表守护执行的进程，如果进程停止则从新开启进程。 >0 : 代表守护次数
     * @return bool
     * @throws Exception
     */
    public function run($run_cmd_times = 1)
    {
        $demo = (int) abs ( $run_cmd_times);
        if(!$demo)
        {
            $run_cmd_times = PHP_INT_MAX;
        }
        $this->stop_process_list = array_combine ( array_keys ( $this->cmd_list_info) , array_fill ( 0 , count($this->cmd_list_info) , $run_cmd_times-1));
        foreach($this->cmd_list_info as $k=>$v)
        {
            $this->process_list[$k] = proc_open ( $v['cmd'] , $v['des'] , $this->cmd_list_info[$k]['pipe'],ROOT_PATH);
            if(!is_resource ( $this->process_list[$k]))
            {
                unset($this->process_list[$k]);
                $this->stop ();
                goto end ;
            }
        }
        return true;
        end:
        throw new Exception('进程创建错误！');
    }

    /**
     * 等待进程执行结束
     * @param bool $block 是否阻塞等待
     * @param float $interval 等待间隔
     */
    public function wait($block = true,  $interval = 1.0)
    {
        do{
            foreach ( $this -> cmd_list_info as $k => $v ) {
                if ( !empty( $this -> process_list[ $k ] ) ) {
                    $status = $this -> processStatus ( $this -> process_list[ $k ] );
                    if ( is_array ( $status ) ) {
                        if ( !$status[ 'running' ] ) {
                            $this -> log ( 'notice',"进程编号为：{$k} 的cmd进程执行结束, {$status['msg']}" );
                            if ( $this -> stop_process_list[ $k ] <= 0 ) {
                                unset( $this -> process_list[ $k ] );
                            }
                            else
                            {
                                $this -> process_list[ $k ] = false;
                                $recreate_msg               = $this -> reCreateProcess ( $k );
                                if ( $recreate_msg ) {
                                    $this -> log ( 'error',$recreate_msg );
                                }
                                else {
                                    $this -> stop_process_list[ $k ] --;
                                }
                            }
                        }
                    }
                    else {
                        $this -> log ('error', "cmd: {$v['cmd']}, $status" );
                    }
                }
            }
            usleep($interval*1000000);
        }while($block && count ($this->process_list)>0);
        $this->log('info','命令进程全部执行结束！');
    }

    /**
     * 向命令行程序发送信号 标志进程唯一的cmd 字符
     * @param bool $flag
     * @param int $signal
     */
    public function stopCmd($flag = false ,$signal = 9)
    {
        foreach($this->cmd_list_info as $v)
        {
            if(!$flag)
            {
                $flag = $v;
            }
            $cmd = "ps aux | grep '{$flag}' | grep -v grep | tail -n 1 | awk '{print \"kill -s {$signal} \"$2 }' | sh" ;
            echo $cmd.PHP_EOL;
            echo shell_exec ($cmd ).PHP_EOL;
        }
    }

    /**
     *
     * @param $resource resource
     * @return array |string
     */
    protected function processStatus($resource)
    {
        $info = proc_get_status ( $resource) ;
        if($info)
        {
            $res['pid'] = $info['pid'];
            $res['cmd'] = $info['command'];
            if($info['running'])
            {
                $res['running'] = true;
                $res['msg'] = '';
            }
            else
            {
                $res['running'] = false;
                $res['msg'] = "pid: {$info['pid']}, 退出码: {$info['exitcode']}".($info['signaled']?", 因信号 {$info['termsig']} 退出":null).($info['stopped']?", 因信号 {$info['stopsig']} 停止!":null);
            }
            return $res;
        }
        else
        {
            return "获取进程状态失败！";
        }
    }

    /**
     *
     * @param $process_no
     * @return string
     */
    protected function reCreateProcess($process_no)
    {
        $this->log ( 'info',"正在重建编号为：{$process_no}的cmd进程！");
        $this->process_list[$process_no] = proc_open ( $this->cmd_list_info[$process_no]['cmd'] , $this->cmd_list_info[$process_no]['des'] , $this->cmd_list_info[$process_no]['pipe'],ROOT_PATH);
        if(!is_resource ( $this->process_list[$process_no]))
        {
            return "重新创建命令进程失败！cmd: {$this->cmd_list_info[$process_no]['cmd']}";
        }
        return null;
    }

    /**
     * 停止进程
     * @param bool $process_no 进程在进程对象中的编号
     * @param int $signal default=SIGKILL
     * @return bool|null
     * @throws Exception
     */
    protected function stop ( $process_no = true , $signal = 9 )
    {
        if ( $process_no === true ) {
            $process_list = $this -> process_list;
        }
        else if ( isset( $this -> process_list[ $process_no ] ) ) {
            $process_list[ $process_no ] = $this -> process_list[ $process_no ];
        }
        else {
            throw new Exception( "进程编号：{$process_no} 不存在！" );
        }
        foreach ( $process_list as $k => $v ) {
            $this -> stop_process_list[$k] = 0;
            proc_terminate ( $v , $signal );
        }
        return true;
    }

    /**
     * 写入日志
     * @param string $level
     * @param string $msg
     */
    protected function log($level, $msg)
    {
        $msg = date('Y-m-d H:i:s')." [{$level}] $msg ".PHP_EOL;
        fwrite ( $this->log_handle , $msg);
    }
}
