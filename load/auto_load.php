<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 1:27
 */

namespace load;

use system\cache\cache;
use system\config\config;
use system\Exception;
use system\file;
require_once __DIR__."/"."provider_register.php";
require_once dirname(__DIR__)."/"."system/config/config.php";
class auto_load
{
    protected $provider;
    protected $file_path;
    public function __construct()
    {
        $this->provider=\load\provider_register::provider();
        $this->file_path = str_replace("\\","/",dirname(__DIR__))."/";
        spl_autoload_register(function ($class){
            $file = str_replace('\\','/',$this->file_path.$class . '.php');
            if (is_file($file)) {
                require_once(@$file);
                return;
            }
            $class_name=explode('\\',$class);
            $class_name=$class_name[count($class_name)-1];
            if(array_key_exists($class_name,$this->provider->get_dependencies())){
                $class_name=str_replace('load\\',"",$this->provider->get_dependencies()[$class_name].".php");
                $class_name= str_replace("\\","/",$class_name);
             //   echo $this->file_path.$class_name."依赖加载===>".PHP_EOL;
                require_once(@$this->file_path.$class_name);
                return;
            }
            if(array_key_exists($class,$this->provider->get_dependencies())){
                require_once(@$this->file_path.$class_name);
                return;
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
    public static function load_extend($project_name){
        $file=new file();
        $provier=provider_register::provider();
        if(!in_array($project_name,array_keys(config::depenendcies()['extend']))){
            new Exception('404','undefined_dependencies_path_please_define_in_config');
        }
        $dependencies=$file->read_file(config::env_path()."filesystem/class_path/$project_name.txt");
        if($dependencies==null||$dependencies==false){
            new Exception('404','dependencies empty use php awesome update');
        }
        $provier->add_dependencies(json_decode($dependencies,true));
    }
}
new auto_load();