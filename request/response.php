<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/15 0015
 * Time: 下午 7:45
 */

namespace request;


use system\Exception;

class response
{
    public function download($file_path,$file_name=false){
        $file_path=iconv('UTF-8','GB2312',$file_path);
        if(!is_file($file_path)){
            new Exception(404,'FILE NOT FIND '.$file_path);
        }
        if($file_name==false){
            $file_name=basename($file_path);
        }
        Header( "Content-type:application/octet-stream");
        Header( "Accept-Ranges:bytes");
        Header( "Accept-Length:");
        header( "Content-Disposition:  attachment;  filename= $file_name");
        return file_get_contents($file_path);
    }
    public function jsonp($callback,$callback_params){
        return sprintf("$callback(%s)", json_encode($callback_params));
    }
}