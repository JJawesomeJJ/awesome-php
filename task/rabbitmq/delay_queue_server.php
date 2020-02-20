<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/7 0007
 * Time: 下午 7:52
 */

namespace task\rabbitmq;
use system\class_define;
use system\common;
use system\system_excu;

require_once __DIR__."/../../load/auto_load.php";
require_once __DIR__."/../../load/common.php";
class delay_queue_server
{
    protected $exchage_name = "delay_exchange_name_";
    protected $queue_name = "delay_queue_name_";
    protected $delay_queue_list="rabbimq_delay";
    protected $route_key="";
    protected $rabbitmq;
    protected $server=null;
    public function __construct()
    {
        $server = new \swoole_server("127.0.0.1", 9999);
        $this->server=$server;
        $server->addProcess(new \swoole_process(function () use($server){
            $rabbitmq=new \task\rabbitmq();
            $rabbitmq->block_handle($this->exchage_name,$this->queue_name,$this->route_key,function ($envelope,$queue)use ($server){
                try {
                    echo "收到数据".PHP_EOL;
                    $msg=$envelope->getBody();
                    $msg=json_decode($msg,true);
                    $unique_id=common::unique_key();
                    $msg['unique_id']=$unique_id;
                    $msg=json_encode($msg);
                    class_define::redis()->hSet($this->delay_queue_list,$unique_id,$msg);
                    $server->task($msg);
                    $queue->ack($envelope->getDeliveryTag());
                }
                catch (\Exception $exception){

                }
            });
        }, false, false));
        $server->set(array('task_worker_num' => 4));
        $server->set([
            'task_enable_coroutine' => true,
        ]);
        $server->on('receive', function($server, $fd, $reactor_id, $data) {
//            $task_id = $server->task("Async");
//            echo "Dispath AsyncTask: [id=$task_id]\n";
        });
        $server->on('Task', function (\Swoole\Server $serv,$task) {
            echo "数据开始处理".PHP_EOL;
            $msg=json_decode($task->data,true);
            $this->handle($msg,$msg['unique_id']);
        });

        $server->on('finish', function ($server, $task_id, $data) {
            echo "AsyncTask[$task_id] finished: {$data}\n";
        });
        $server->on('start',function () use ($server){
            system_excu::record_my_pid(__FILE__);
            $this->onstart($server);
        });
        $server->start();
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
    protected function onstart($server){
        $data=class_define::redis()->hGetAll($this->delay_queue_list);
        foreach ($data as $unique_id=>$msg){
            $server->task($msg);
        }
    }
    protected function handle($msg,$unique_id){
        try {
            if (($time = $this->resolve_time($msg['created_at'], $msg['delay_time'])) > 0) {
                $result=$this->server->after($time, function () use ($msg, $unique_id) {
                    echo '延迟消息已被释放'.PHP_EOL;
                    $rabbitmq = new \task\rabbitmq();
                    $rabbitmq->push($msg['exchange_name'], $msg['queue_name'], $msg['msg'], $msg['router_key']);
                    class_define::redis()->hDel($this->delay_queue_list, $unique_id);
                });
                while (!is_numeric($result)){
                    $result=$this->server->after($time, function () use ($msg, $unique_id) {
                        $rabbitmq = new \task\rabbitmq();
                        $rabbitmq->push($msg['exchange_name'], $msg['queue_name'], $msg['msg'], $msg['router_key']);
                        class_define::redis()->hDel($this->delay_queue_list, $unique_id);
                    });
                }
            } else {
                echo "消息立刻处理".PHP_EOL;
                $rabbitmq = new \task\rabbitmq();
                $rabbitmq->push($msg['exchange_name'], $msg['queue_name'], $msg['msg'], $msg['router_key']);
                class_define::redis()->hDel($this->delay_queue_list, $unique_id);
            }
        }
        catch (\Throwable $exception){
            $log=new \system\log();
            $log->write_log($exception);
        }
    }
}
new delay_queue_server();