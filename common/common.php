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

function safe_mkdir($path, $mode = 0777)
{
    if(is_dir($path))
    {
        return true;
    }
    $lock_file = TMP_PATH.md5($path).'.lock';
    $lock_handle = fopen($lock_file,'a');
    if(is_resource ( $lock_handle))
    {
        if(flock ( $lock_handle , LOCK_EX|LOCK_NB))
        {
            if(is_dir($path))
            {
                $res = true;
            }
            else
            {
                $res = mkdir($path,$mode,true);
            }
            flock($lock_handle,LOCK_UN);
        }
        else{
            $res = true ;
        }
        fclose ( $lock_handle);
    }
    else
    {
        $res = false ;
    }
    return $res;
}