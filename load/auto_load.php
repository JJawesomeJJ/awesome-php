<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 1:27
 */

namespace load;

use system\config\config;

class auto_load
{
    public function __construct()
    {
        spl_autoload_register(function ($class){
            $file_path=explode("php/",__DIR__)[0]."php/";
            $file = str_replace("\\","/",$file_path.$class . '.php');
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
}
new auto_load();