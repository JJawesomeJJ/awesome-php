<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/6 0006
 * Time: 下午 10:55
 */

namespace task\queue;


use system\config\config;
use system\Exception;
use task\job\email_queue;

abstract class queue_handle
{
    protected $listen_queue="";
    protected $redis=null;
    protected $max_time_out=60;
    protected $now_wait_time=0;
    protected $sleep_time=10;
    public function __construct()
    {
        $this->redis=new \Redis();
        $this->redis->connect(config::redis()["host"],config::redis()["port"]);
        $this->redis->hSet(\system\config\queue::$job_list,$this->listen_queue,date("Y-m-d H:i:s"));
        $this->get_queue();
    }
    public function get_queue(){
        echo "开始处理".PHP_EOL;
        while($this->redis->lLen($this->listen_queue)>0){
            $this->now_wait_time=0;
            $queue_data=unserialize($this->redis->lPop($this->listen_queue));
            try{
                echo "处理中".PHP_EOL;
                $this->handle($queue_data);
            }
            catch (\Throwable $throwable){
                echo "出现异常".$throwable->getMessage().PHP_EOL;
                $queue_data["fail"]=$throwable->getMessage();
                $this->redis->rPush($this->listen_queue."fail",serialize($queue_data));
                //处理失败将该任务推入失败的队列并写入错误日志
            }
            catch (\Throwable $throwable){
                $this->onfinish();
                new Exception("500",$throwable->getMessage());
            }
        }
        $this->now_wait_time=$this->now_wait_time+$this->sleep_time;
        if($this->now_wait_time<$this->max_time_out){
            sleep($this->sleep_time);
            $this->get_queue();
        }
    }//开始处理队列中的任务 需要重写具体的handle方法
    abstract function handle(array $queue);
    public function onfinish(){
        $this->redis->hDel(\system\config\queue::$job_list,$this->listen_queue);
    }
    public function __destruct()
    {
        echo "程序执行完毕".PHP_EOL;
        $this->onfinish();
        //当队列处理完毕，将队列处理的标志位删除
    }
}