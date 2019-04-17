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
    public static function cache(){
        return self::$cache;
    }
    public static function session(){
        return self::$session;
    }
}