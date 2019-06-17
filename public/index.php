<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 3:01
 */
header('Access-Control-Allow-Origin:*');
define("home_path",dirname(__DIR__));
use routes\routes;
class index
{
    public function __construct()
    {
        $GLOBALS["time"]=microtime(true);
        require_once __DIR__."/../load/auto_load.php";
        require_once __DIR__."/../routes/route_entrance.php";
        require_once __DIR__."/../load/common.php";
        try {
            new routes();
        }
        catch (Throwable $throwable){
            echo $throwable;
            app()->call_back();
        }
    }
}
$index=new index();