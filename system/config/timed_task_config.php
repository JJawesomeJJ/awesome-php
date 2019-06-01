<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14 0014
 * Time: 下午 1:11
 */

namespace system\config;


class timed_task_config
{
    public static $home_path;
    public static function timed_task_command(){
        if(self::$home_path==null){
            self::$home_path=dirname(dirname(__DIR__));
        }
        return [
            "clear_expire_cache"=>self::$home_path."/task/timed_task/timed_task_list/clear_expire_cache.php",
            "closure"=>self::$home_path."/task/timed_task/timed_task_list/closure_task.php"
        ];
    }//此处配置具体的定时任务队列任务
    public static function timed_task_schedule(){
        if(self::$home_path==null){
            self::$home_path=dirname(dirname(__DIR__));
        }
        return[
            self::$home_path."/task/timed_task/timed_task_list/timed_task_schedule.php",
        ];
    }
}