<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 1:27
 */

namespace load;

use system\config\config;
use system\Exception;

class auto_load
{
    public function __construct()
    {
        spl_autoload_register(function ($class){
//            echo $class."<br>";
            $file = str_replace("\\","/",dirname(__DIR__)."/".$class . '.php');
            if (is_file($file)) {
                require_once(@$file);
            }
            else{
                foreach (config::class_path() as $key=>$value){
                    if(strpos($file,$key)!==false){
                        $file=str_replace($key,$value,$file);
                        $file=str_replace("\\","/",$file);
                        if(is_file($file)){
                            require_once(@$file);
                            break;
                        }
                    }
                }
            }
        });
    }
    public static function load($class_path,$is_absolute=false){
        $base_path=dirname(__DIR__)."/extend/";
        if($is_absolute){
            $base_path="";
        }
        $class_path=str_replace("\\","/",$class_path);
        $class_path=str_replace(".","/",$class_path);
        $file_path=$base_path.$class_path.".php";
        if(is_file($file_path=$base_path.$class_path.".php")){
            require_once $file_path;
        }
        else{
            new Exception("400","load_file_$file_path _error");
        }
    }
}
new auto_load();