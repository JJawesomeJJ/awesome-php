<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/5 0005
 * Time: 下午 9:00
 */

namespace task\timed_task\timed_task_list;


use function PHPSTORM_META\type;
use system\cache\cache;
use system\config\config;
use system\file;
use system\http;
use task\timed_task\timed_task_handle;
require_once __DIR__."/../../../load/auto_load.php";

class swoole_dev extends timed_task_handle
{
    public function start()
    {
        $is_dev=false;
        $cache=new cache();
        $file=new file();
        $except=config::swoole_dev()['except'];
        $scan_path=config::swoole_dev()['scan'];
        foreach ($scan_path as $path){
            $file_list=$file->file_walk($path,$except);
            foreach ($file_list as $file_name){
                if(($md=$cache->get_non_exist_set($file_name,function () use ($file_name){
                    echo "文件未加载跳过".PHP_EOL;
                    return md5_file($file_name);},'forever',false,false))===true){
                    continue;
                }
                if($md!=($md5=md5_file($file_name))){
                    $cache->set_cache($file_name,$md5,'forever');
                    echo "不同应该热更新".PHP_EOL;
                    $is_dev=true;
                }
            }
        }
        if($is_dev){
            $http=new http();
            $http->post("http://".config::server()["host_ip"].":9555",["password"=>19971998]);
            echo "load";
            $is_dev=false;
        }
    }

}
(new swoole_dev())->start();