<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 3:01
 */
header('Access-Control-Allow-Origin:*');
define("extend",__DIR__.'/../extent/');
define("routes",__DIR__.'/../routes/');
define("system",__DIR__.'/../system/');
define("request",__DIR__.'/../request/');
define("db",__DIR__."/../db/");
use controller\auth\auth_controller;
use routes\routes;
use routes\route_entrance;
class index
{
    public function __construct()
    {
        require_once __DIR__."/../load/auto_load.php";
        $routes=new route_entrance();
    }
}
$index=new index();