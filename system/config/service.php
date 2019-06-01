<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/30 0030
 * Time: 下午 10:13
 */

namespace system\config;


abstract class service
{
    protected static $service_config_list=[];
    protected static $home_path;
    protected static $service_object;
    public function __construct()
    {
        if(self::$home_path==null){
            self::$home_path=dirname(dirname(__DIR__));
        }
        $this->service();
    }
    public static function service_config(){
        if(self::$service_object==null){
            self::$service_object=new service_config();
        }
        return self::$service_config_list;
    }
    protected function add_service_config($task_name,$service_script_name){
        self::$service_config_list[$task_name]=$service_script_name;
    }
    abstract protected function service();
}