<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/6 0006
 * Time: 下午 12:57
 */

namespace task\queue;
use SuperClosure\Serializer;
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
    }//检查任务队列标识位是否需要创建进程进行处理；
    public static function asyn(\Closure $function){
        if(!$function instanceof \Closure){
            new Exception("403","please use Closure");
        }
        $queue=new queue();
        $serialize=new Serializer();
        $function=$serialize->serialize($function);
        $queue->push("asyn",$function,"asyn");
    }//创建一个异步任务默认接受一个闭包
    //闭包php默认不可以吧被序列化我们采用superclosure 进行对闭包的序列化
}