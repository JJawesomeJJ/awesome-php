<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/17 0017
 * Time: 下午 6:06
 */

namespace system\kernel;


use system\Exception;

abstract class facede
{
    public static function __callStatic($func,$arguments){
        $class_name=make_method("getFacadeAccessor",get_called_class());
        $object=make($class_name);
        if(!method_exists($object,$func)){
            new Exception(404,"method undefined $func");
        }
        return call_user_func_array([$object, $func], $arguments);
    }
    abstract function getFacadeAccessor();
}