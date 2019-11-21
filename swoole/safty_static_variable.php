<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/27 0027
 * Time: 下午 9:21
 */

namespace swoole;


class safty_static_variable
{
    static $obejct;
    protected function __construct()
    {

    }
    public static function newInstance(){
        if(is_null(self::$obejct)){
            self::$obejct=new safty_static_variable();
        }
        return self::$obejct;
    }
    public function set($class_object,$var,$value){
        $var_name=md5($class_object).$var;
        $this->$var_name=$value;
    }
    public function get($class_object,$var){
        $var_name=md5($class_object).$var;
        if(isset($this->$var_name)){
            return $this->$var_name;
        }
        return null;
    }
    public function __destruct()
    {
        self::$obejct=null;
    }
}