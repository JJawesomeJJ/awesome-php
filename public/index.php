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
        require_once __DIR__."/../load/auto_load.php";
        require_once __DIR__."/../load/common.php";
        try {
            $route=new routes();
            if(!is_cli()) {
                require_once __DIR__ . "/../routes/route_entrance.php";
            }else{
                require_once __DIR__ . "/../routes/route_console.php";
            }
            $route->start();
        }
        catch (Throwable $throwable){
            if(\system\config\config::debug()['status']) {
                echo $throwable;
            }
            app()->call_back();
            $log=new \system\log();
            $log->write_log($throwable);
        }
    }
}
define("DIR_PATH",dirname(__DIR__)."/");
define("WWW_PATH",__DIR__."/");
define('start_at',microtime(true));//定义程序初始化时间
$index=new index();