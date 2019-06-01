<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/6 0006
 * Time: 下午 9:57
 */

namespace system\config;


class queue
{
    public static $home_path=null;
    public static $record_list="record_list";
    public static $job_list="queue_on_work";
    public static function queue_handle(){
        if(self::$home_path==null){
            self::$home_path=dirname(dirname(__DIR__));
        }
        return [
            "email"=>self::$home_path."/task/job/email_queue.php",
            "asyn"=>self::$home_path."/task/job/asyn_queue.php"
        ];
    }//此处配置
    public static function homepath(){
        if(self::$home_path==null){
            self::$home_path=dirname(dirname(__DIR__));
        }
        return self::$home_path;
    }

}