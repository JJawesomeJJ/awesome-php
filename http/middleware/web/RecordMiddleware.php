<?php

namespace http\middleware\web;
use http\middleware\middleware;
use request\request;
use system\class_define;
use system\config\config;
use system\file;
use system\lock;
use system\redis;

class RecordMiddleware extends middleware
{
    public function check()
    {
        $request=request::SingleTon();
        $content=sprintf("time:[%s] ip:[%s] url:[%s] params[%s] method[%s]".PHP_EOL,date("Y-m-d H:i:s"),$request->get_ip_address(),$request->get_url(),json_encode($request->all()),$request->request_mothod());
        $file_path=config::env_path().'/filesystem/log/request/'.date("Y-m-d").".txt";
        $file=new file();
        $path=dirname($file_path);
        if(!is_dir($path)) {
            $file->mkdir($path,0777);
        }
        file_put_contents($file_path, $content, FILE_APPEND|LOCK_EX);
    }
}