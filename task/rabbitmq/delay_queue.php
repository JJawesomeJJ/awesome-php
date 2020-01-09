<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/28 0028
 * Time: 下午 9:11
 */

use system\class_define;
use system\common;

require_once __DIR__."/../../load/auto_load.php";
require_once __DIR__."/../../load/common.php";
class delay_queue{
    protected $exchage_name = "delay_exchange_name_";
    protected $queue_name = "delay_queue_name_";
    protected $delay_queue_list="rabbimq_delay";
    protected $route_key="";
    protected $rabbitmq;
    protected $process_sleep_time=10*1000;
    protected $current_run_time="";
    protected $timer_object=null;
    public function __construct()
    {
        $rabbitmq=new \task\rabbitmq();
        $parent_pid=getmypid();
        $timer = new swoole_process(function(swoole_process $worker){
            $this->timer_object=$this->start_timer($worker);
        }, false, false);
        $timer->useQueue(9502,swoole_process::IPC_NOWAIT);
        $delay_task_pid=$timer->start();
        $process = new swoole_process(function (swoole_process $worker) use($timer){
                $rabbitmq=new \task\rabbitmq();
                $rabbitmq->block_handle($this->exchage_name,$this->queue_name,$this->route_key,function ($envelope,$queue)use ($timer){
                    try {
                        $msg=$envelope->getBody();
                        $msg=json_decode($msg,true);
                        $unique_id=common::unique_key();
                        $msg['unique_id']=$unique_id;
                        $msg=json_encode($msg);
                        class_define::redis()->hSet($this->delay_queue_list,$unique_id,$msg);
                        while ($timer->push($msg)==false){

                        }
                        $queue->ack($envelope->getDeliveryTag());
                    }
                    catch (Exception $exception){

                    }
            });
        }, false, false);
        $process->useQueue(9502,swoole_process::IPC_NOWAIT);
        $listen_pid=$process->start();
        $this->onstart($timer);
        \system\system_excu::record_my_pid(__FILE__,$delay_task_pid.'_'.$listen_pid);
    }
    protected function start_timer($worker){
        return swoole_timer_tick(1,function () use ($worker){
            $msg = $worker->pop();
            if (!empty($msg)) {
                $msg=json_decode($msg,true);
                $this->handle($msg,$msg['unique_id']);
            }else{
                if($this->current_run_time>100){
                    Swoole\Timer::clear($this->timer_object);
                    swoole_timer_after($this->process_sleep_time,function ()use ($worker){
                        $this->start_timer($worker);
                    });
                }
            }
        });
    }
    protected function handle($msg,$unique_id){
        try {
            if (($time = $this->resolve_time($msg['created_at'], $msg['delay_time'])) > 0) {
                $result=swoole_timer_after($time, function () use ($msg, $unique_id) {
                    $rabbitmq = new \task\rabbitmq();
                    $rabbitmq->push($msg['exchange_name'], $msg['queue_name'], $msg['msg'], $msg['router_key']);
                    class_define::redis()->hDel($this->delay_queue_list, $unique_id);
                });
                while (!is_numeric($result)){
                    $result=swoole_timer_after($time, function () use ($msg, $unique_id) {
                        $rabbitmq = new \task\rabbitmq();
                        $rabbitmq->push($msg['exchange_name'], $msg['queue_name'], $msg['msg'], $msg['router_key']);
                        class_define::redis()->hDel($this->delay_queue_list, $unique_id);
                    });
                }
            } else {
                $rabbitmq = new \task\rabbitmq();
                $rabbitmq->push($msg['exchange_name'], $msg['queue_name'], $msg['msg'], $msg['router_key']);
                class_define::redis()->hDel($this->delay_queue_list, $unique_id);
            }
        }
        catch (Throwable $exception){
            $log=new \system\log();
            $log->write_log($exception);
        }
    }
    //解析延迟时间
    //当时间大于0表示延时执行党时间小于表示立即执行
    protected function resolve_time($created_at,$dealy_time){
        $time=$created_at+$dealy_time-time();
        if($time>0){
            return $time*1000;//s=>ms
        }
        return 0;
    }
    //当任务开始执行的时候检查redis里面的副本重新加入定时器
    protected function onstart($timer){
        $data=class_define::redis()->hGetAll($this->delay_queue_list);
        foreach ($data as $unique_id=>$msg){
            $timer->push($msg);
        }
    }

}
new delay_queue();