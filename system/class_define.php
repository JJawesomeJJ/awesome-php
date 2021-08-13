<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/30 0030
 * Time: 下午 7:42
 */

namespace system;


use system\config\config;

class class_define
{
    protected static $redis;
    public static function redis(){
        if(self::$redis==null){
            $config = config::redis();
            self::$redis=new \Redis();
            self::$redis->connect($config["host"],$config["port"]);
            if (!empty($config['password'])){
                self::redis()->auth($config['password']);
            }
            if (!empty($config['index'])){
                self::redis()->select($config['index']);
            }
        }
        return self::$redis;
    }
}