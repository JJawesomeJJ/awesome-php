<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 3:01
 */
header('Access-Control-Allow-Origin:*');
use routes\routes;
class index
{
    public function __construct()
    {
        $GLOBALS["time"]=microtime(true);
        require_once __DIR__."/../load/auto_load.php";
        require_once __DIR__."/../load/common.php";
        \system\config\config::env_path();
        try {
            $route=new routes();
            require_once __DIR__."/../routes/route_entrance.php";
            $route->start();
        }
        catch (Throwable $throwable){
            if(\system\config\config::debug()) {
                echo $throwable;
                app()->call_back();
            }
            $log=new \system\log();
            $log->write_log($throwable);
        }
    }
}
$index=new index();