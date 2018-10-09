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
// | Time: 下午 23:56
// +----------------------------------------------------------------------

namespace bin\tool;

use Exception;

trait net
{
    /**
     *
     * @param $url
     * @param array $data
     * @param string $method
     * @param int $timeout
     * @return string
     * @throws Exception
     */
    protected function request ( $url , array $data , $method = 'POST' , $timeout = 30 )
    {
        $data = http_build_query ( $data );
        $method = strtoupper ( $method ) ;
        $ch   = curl_init ();
        curl_setopt_array ( $ch , [
            CURLOPT_URL => $url ,
            CURLOPT_CONNECTTIMEOUT => $timeout ,
            CURLOPT_CUSTOMREQUEST => strtoupper ( $method ) ,
            CURLOPT_HEADER => 0 ,
            CURLOPT_RETURNTRANSFER => 1 ,
            CURLOPT_HTTPHEADER => [ 'Content-Type: application/json; charset=utf-8' , 'Content-Length: ' . strlen ( $data ), ] ,
            CURLOPT_POST => $method=='POST'?1:0 ,
            CURLOPT_POSTFIELDS => $data ,
            CURLOPT_SSL_VERIFYPEER => 0 ,
            CURLOPT_SSL_VERIFYHOST => 0 ,
        ] );
        $result = curl_exec ( $ch );
        if ( curl_errno ( $ch ) ) {
            throw new Exception( curl_error ( $ch ) );
        }
        return $result;
    }
}