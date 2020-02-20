<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/23 0023
 * Time: 下午 9:59
 */

namespace swoole;


use extend\awesome\awesome_driver_rabbitmq;
use extend\awesome\awesome_echo_tool;
use system\class_define;
use system\common;
use system\config\channel_config;
use system\LuaScript;
use system\system_excu;
use task\rabbitmq;
require_once __DIR__."/../load/auto_load.php";
require_once __DIR__."/../load/common.php";
class awesome_echo
{
    public $server;
    protected $redis;
    protected $channel=[];
    protected $config;
    protected $listen_work_num=1;//监听rabbitmq的数量
    protected $awesome_echo_tool;
    public function __construct() {
//        $this->config=channel_config::awesome_echo();
        $this->config=[
            'online'=>'awesome_echo_online',//记录当前在线的用户-record online user
            'channel'=>'awesome_echo_channel',//记录当前所有的频道-record current all channel
            'channel_suffix'=>'awesome_echo',//频道的前缀
            'user_rel_id'=>'awesome_echo_online_real',//记录当前已经验证的用户的id
            'user_token'=>'awesome_token',
            'cache_key'=>'user_cache'
        ];
        $this->awesome_echo_tool=new awesome_echo_tool(new awesome_driver_rabbitmq());
        ini_set('default_socket_timeout', -1);
        $this->redis=class_define::redis();
        $this->onrestart();
        $this->server = new \Swoole\WebSocket\Server("0.0.0.0", channel_config::config()['port']);
        $this->server->set(array(
            'worker_num' => 2,
            'task_worker_num' => 4,
            'heartbeat_idle_time' => 30,
            'heartbeat_check_interval' => 5,
        ));
        $this->server->on('start',function (){
            system_excu::record_my_pid(__FILE__,$this->server->master_pid);
        });
        $this->server->on('open', function (\swoole_websocket_server $server, $request) {
            $this->redis->hSet($this->config['online'],$request->fd,json_encode([]));//加入在线的用户列表
            $unique_id=common::unique_key();
            $this->redis->hSet($this->config['user_token'],$request->fd,$unique_id);//为用户分配唯一的token验证用户的身份
            $this->server->push($request->fd,json_encode([
                'handle'=>"token",
                "_token"=>$unique_id,
                "fd"=>$request->fd
            ]));
        });
        $this->server->on('close', function ($ser, $fd) {
            $this->onuserclose($fd);
        });
        for($i=0;$i<$this->listen_work_num;$i++) {
            $this->server->addProcess(new \swoole_process(function () {
                $this->handdle_channel();//开启工作线程监听从rabbitmq发送的数据
            }));
        }
//         $this->server->addProcess(new \swoole_process(function () {
//             swoole_timer_tick(2000, function ($timer_id) {
//                 foreach ($this->server->connections as $fd) {
//                     if(!$this->server->exist($fd)){
//                         echo "close_by_listen";
//                         $this->onuserclose($fd);
//                     }
//                 }
//             });
//         }));
        $this->server->on('Task', function (\Swoole\Server $serv, $task_id, $from_id, $data) {
            print_r($data);
            switch ($data['type']){
                case 'notice_channel':
                    $this->notice_channel($data);
                    break;
                case 'notice_user':
                    if($this->server->isEstablished(number_format($data['fd']))){
                        try {
                            $this->server->push($data['fd'],json_encode($data['data']));
                        }
                        catch (\Throwable $throwable){
                            $this->onuserclose($data['fd']);
                        }
                    }else{
                        $this->onuserclose($data['fd']);
                    }
                    break;
                case 'add_channel'://将用户加入频道
                    LuaScript::hash_add_hash($this->config['channel_suffix'].$this->config['channel'],$data['channel_name'],$data['fd'],time());
                    LuaScript::hash_add_hash($this->config['online'],$data['fd'],$data['channel_name'],time());
                    break;
                case "send_to_user":
                    $this->send_to_user($data);
                    break;
                default:
                    break;
            }
//            $serv->finish($data);
        });
        $this->server->on('message', function (\Swoole\WebSocket\Server $server, $frame) {
//            echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
//            $server->push($frame->fd, "this is server");
        });

        $this->server->start();
    }
    //开启一个进程处理从服务器发送的数据
    protected function handdle_channel(){
        $config=channel_config::config();
        if($config['driver']=='rabbitmq'){
            $rabbitmq=new rabbitmq();
            $rabbitmq_config=channel_config::rabbitmq();
            $rabbitmq->block_handle($rabbitmq_config['exchange'],$rabbitmq_config['queue'],'',function ($envelope,$queue){
                $msg=$envelope->getBody();
                try{
                    $msg=json_decode($msg,true);
                    $queue->ack($envelope->getDeliveryTag());
                    $this->server->task($msg);
                }
                catch (\Throwable $throwable){
                    echo $throwable.PHP_EOL;
                }
            });
        }
    }

    /**
     * @description where user leave close connection close the channel
     * @param $fd
     */
    protected function onuserclose($fd){
        $channel_info=json_decode($this->redis->hGet($this->config['online'],$fd),true);
        if(!is_array($channel_info)){
            $channel_info=[];
        }
        foreach ($channel_info as $key=>$value){
            LuaScript::hash_del_key($this->config['channel_suffix'].$this->config['channel'],$key,$fd);
            $this->awesome_echo_tool->send_to_channel($key,['msg'=>'用户离开了','fd'=>$fd],'leave',false);
        }
        $this->redis->hDel($this->config['online'],$fd);
        $this->redis->hDel($this->config['user_rel_id'],$fd);
        $this->redis->hDel($this->config['user_token'],$fd);
    }
    //当服务器重启的时候清除掉过去的数据
    protected function onrestart(){
        $this->redis->del($this->config['channel_suffix'].$this->config['channel']);
        $this->redis->del($this->config['channel']);
        $this->redis->del($this->config['online']);
        $this->redis->del($this->config['user_rel_id']);
    }

    /**
     * @description 用户离开频道
     * @param $channel_name
     * @param $fd
     */
    protected function leave_channel($channel_name,$fd){
        LuaScript::hash_del_key($this->config['online'],$fd,$channel_name);
        LuaScript::hash_del_key($this->config['channel_suffix'].$this->config['channel'],$channel_name,$fd);
    }

    /**
     * @description 发送某个数据到频道
     * @param array $data
     */
    protected function notice_channel(array $data){
        $channel_name=$this->config['channel_suffix'].$this->config['channel'];
        $channel_info=$this->redis->hGet($channel_name,$data['channel_name']);
        $channel_info=json_decode($channel_info,true);
        if(!is_array($channel_info)){
            $channel_info=[];
        }
        foreach ($channel_info as $key=>$value){
            unset($data['type']);
            $msg=$data['data'];
            if(!is_string($msg)){
                $msg=json_encode($msg);
            }
            if($this->server->isEstablished(number_format($key))){
                try {
                    $this->server->push($key,$msg);
                }
                catch (\Throwable $throwable){
                    $this->onuserclose($key);
                }
            }else{
                $this->onuserclose($key);
            }
        }
    }

    /**
     * @description 发送一条数据到用户
     * @param array $msg
     */
    protected function send_to_user(array $msg){
        foreach ($msg['user_id'] as $item){
            $user_msg=$msg;
            unset($user_msg['user_id']);
            $fd=$this->redis->hGet($this->config['user_rel_id'],$item);
            if(is_string($user_msg)){
                $user_msg=json_encode($user_msg);
            }
            if($this->server->isEstablished($fd)){
                try {
                    $this->server->push($fd,$user_msg);
                }
                catch (\Throwable $throwable){
                    $this->onuserclose($fd);
                }
            }else{
                /**
                 * 如果该消息需要缓存将他载入缓存
                 */
                if($msg['is_cache']){
                    LuaScript::hash_add_array($this->config['cache_key'],$fd,$user_msg);
                }
            }
        }
    }
}
new awesome_echo();