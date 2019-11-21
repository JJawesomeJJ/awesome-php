<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/1 0001
 * Time: 下午 10:44
 */

namespace swoole;

use routes\routes;
use system\config\config;
require_once __DIR__."/../load/auto_load.php";
class http
{
    protected $http;
    protected $routes=null;
    protected $cache;
    protected $local_ip_address;
    public function __construct()
    {
        $this->local_ip_address="39.108.236.127";
        $this->http = new \swoole_http_server('0.0.0.0', 8888);
        $this->http->set(['worker_num' => 8,
            'max_request' => 5000,
            'daemonize' => 0,
//    'document_root' => '/Users/apple/Code/Teacher_Project/swoole_live/resources/live/',
//    'enable_static_handler' => true,
        ]);

//工作进程启动
        $this->http->on('WorkerStart', function ($serv, $worker_id) {
            //加载index文件的内容
        });

//监听http请求
        $this->http->on('request', function ($request, $response) {
            if($this->check_is_dev($request)){
                return;
            }
            ob_start();
            $this->init_request($request);
            if(!class_exists("index")) {
                $index=require __DIR__ . "/../public/index.php";
            }
            else{
                $index=new \index();
            }
            $index=null;
            $res = ob_get_contents();//获取缓存区的内容
            ob_end_clean();//清除缓存区
            //输出缓存区域的内容
            $response->header("Content-type"," text/html; charset=utf-8");
            $response->end($res);
            $this->http->reload();
        });
    }
    protected function init_request($request){
        if (isset($request->server)) {
            foreach ($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
            $_SERVER['PHP_SELF']=$request->server["path_info"];
        }
        //header头信息
        if (isset($request->header)) {
            foreach ($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        //get请求
        if (isset($request->get)) {
            foreach ($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }
        //post请求
        if (isset($request->post)) {
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }
        //文件请求
        if (isset($request->files)) {
            foreach ($request->files as $k => $v) {
                $_FILES[$k] = $v;
            }
        }
        //cookies请求
        if (isset($request->cookie)) {
            foreach ($request->cookie as $k => $v) {
                $_COOKIE[$k] = $v;
            }
        }
    }
    public function start(){
        $this->http->start();
    }
    protected function check_is_dev($request){
        if($request->header['x-real-ip']==$this->local_ip_address){
            if($request->post["password"]=="19971998"){
                $this->http->reload();
                return true;
            }
            return false;
        }
        return false;
    }
}
$http=new http();
$http->start();