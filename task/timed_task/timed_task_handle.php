<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14 0014
 * Time: 下午 9:32
 */

namespace task\timed_task;


use extend\PHPMailer\Exception;
use system\config\config;

abstract class timed_task_handle
{
    protected $task_name="";
    protected $redis;
    public function __construct()
    {
        require_once __DIR__."/../../load/common.php";
        $this->redis=new \Redis();
        $this->redis->connect(config::redis()["host"],config::redis()["port"]);
        $this->redis->hSet("timed_task_worker",$this->task_name,time());
        try {
            $this->start();
        }
        catch (\Throwable $throwable){
            if(!$this->redis->hExists("timed_task_error",$this->task_name)){
                $this->redis->hSet("timed_task_error",$this->task_name,json_encode([["error"=>$throwable->getMessage(),"time"=>time()]]));
            }
            else{
                $error=json_encode($this->redis->hGet("timed_task_error",$this->task_name));
                $error[]=["error"=>$throwable->getMessage(),"time"=>time()];
                $this->redis->hSet("timed_task_error",$this->task_name,json_encode($error));
            }
            $this->redis->hDel("timed_task_worker",$this->task_name);
        }
    }
    abstract function start();
}