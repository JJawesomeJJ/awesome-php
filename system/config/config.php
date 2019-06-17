<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 10:47
 */

namespace system\config;


use request\request;
use system\cache\cache;

class config
{
    protected static $cache=[
        "driver"=>"file",//you can choose faster driver like redis
        "path"=>"filesystem"//defalut path
    ];
    //we support driver redis and file until now may we will provide new driver but writer just a college student no time to do this; cretae_at_2019/4/11/ 9:24
    //cache config
    protected static $session=[
        "name"=>"ssid",
        "driver"=>"redis",//you can choose faster driver like redis
        "path"=>"filesystem\\session\\",//if you choose redis the path is redis HASHKEY
    ];
    //session config
    //when test redis and file as cache driver but i find when data not enough much file faster than redis write 2019/4/12
    protected static $dependendcies=[
        "system",
        "request",
        "extend/test/",
        "db"
    ];
    //dependendcies_config
    //the path awesome_cli will load dependendies use php awesome load
    public static function cache(){
        return self::$cache;
    }
    public static function session(){
        return self::$session;
    }
    public static function depenendcies(){
        return self::$dependendcies;
    }
    public static function database(){
        return [
            "type"=>"mysql",
            "hostname"=>"127.0.0.1",
            "hostport"=>"3306",
            "database"=>"register",
            "username"=>"root",
            "password"=>".zlj19971998",
        ];
    }
    public static function task_record_list(){
        return [
            "name"=>"task_record_list",
        ];
    }
    public static function redis(){
        return [
            "host"=>"127.0.0.1",
            "port"=>"6379"
        ];
    }
    public static function class_path(){
        return [
            "SuperClosure"=>"extend/SuperClosure/src",
            "PhpParser"=>"extend/SuperClosure/vendor/nikic/php-parser/lib/PhpParser"
        ];//define class_path
    }
    public static function server(){
        return[
           "host_ip"=>"39.108.236.127"
        ];
    }
}