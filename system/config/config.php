<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 10:47
 */

namespace system\config;


use system\cache\cache;

class config
{
    protected static $cache=[
        "driver"=>"redis",//you can choose faster driver like redis
        "path"=>"filesystem"//defalut path
    ];
    //we support driver redis and file until now may we will provide new driver but writer just a college student no time to do this; cretae_at_2019/4/11/ 9:24
    //cache config
    protected static $session=[
        "driver"=>"redis",//you can choose faster driver like redis
        "path"=>"filesystem\\session\\",//if you choose redis the path is redis HASHKEY
    ];
    //session config
    //when test redis and file as cache driver but i find when data not enough much file faster than redis write 2019/4/12
    protected static $dependendcies=[
        "system",
        "extend"
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
            "username"=>"register",
            "password"=>"zlj19971998",
        ];
    }
    public static function redis(){
        return [
            "host"=>"127.0.0.1",
            "port"=>"6379"
        ];
    }
}