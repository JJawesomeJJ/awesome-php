<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 1:27
 */

namespace load;

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
        });
    }
}
new auto_load();