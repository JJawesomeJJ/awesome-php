<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/6 0006
 * Time: 下午 12:57
 */

namespace task\queue;
use system\config\config;
use system\Exception;

class queue
{
    protected $redis;
    public function __construct()
    {
        $config=config::redis();
        $this->redis=new \Redis();
        $this->redis->connect($config["host"],$config["port"]);
    }
    public function push($job,$data,$queue){
        $this->redis->hSet(\system\config\queue::$record_list,$queue,date("Y-m-d H:i:s"));
            $this->redis->lPush($queue,serialize(["job"=>$job,"data"=>$data]));
            $this->is_handle($queue);
    }//即刻执行的队列
    public function show_queue(){
        return $this->redis->hGetAll(\system\config\queue::$record_list);
    }//检查当前存在的队列
    protected function is_handle($queue_handle){
        if(!$this->redis->hExists(\system\config\queue::$job_list,$queue_handle)){
            $command_array=\system\config\queue::queue_handle();
            if(!array_key_exists($queue_handle,$command_array)){
                new Exception("350","queue_handle_undefine");
            }
            $command=$command_array[$queue_handle];
            exec("php $command".' > /dev/null &');
        }
    }
}