<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/23 0023
 * Time: 下午 9:59
 */

namespace swoole;


use system\class_define;
use system\config\channel_config;
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
    public function __construct() {
        $this->config=channel_config::awesome_echo();
        $this->redis=class_define::redis();
        $this->onrestart();
        $this->create_channel('event','');
        $this->server = new \Swoole\WebSocket\Server("0.0.0.0", channel_config::config()['port']);
        $this->server->set(array(
            'worker_num' => 2,
            'task_worker_num' => 4,
        ));
        $this->server->on('start',function (){
            system_excu::record_my_pid(__FILE__,$this->server->master_pid);
        });
        $this->server->on('open', function (\swoole_websocket_server $server, $request) {
            $this->redis->hSet($this->config['online'],$request->fd,json_encode([]));
        });
        $this->server->on('message', function (\Swoole\WebSocket\Server $server, $frame) {
            $data=json_decode($frame->data,true);
            switch ($data['type']){
                case 'add_channel':
                    $this->vertify_user($frame->fd,$data['key'],$data['channel']);
                    break;
                default:
                    break;
            }
        });
        $this->server->on('close', function ($ser, $fd) {

        });
        $this->server->addProcess(new \swoole_process(function (){
            $this->handdle_channel();
        }));
        $this->server->on('Task', function (\Swoole\Server $serv, $task_id, $from_id, $data) {
            switch ($data['type']){
                case 'create_channel':
                    $this->create_channel($data['channel_name'],$data['password']);
                    break;
                case 'notice_channel':
                    $channel_name=$this->config['channel_suffix'].$data['channel_name'];
                    foreach ($this->redis->hGetAll($channel_name) as $key=>$value){
                        unset($data['type']);
                        if($this->server->isEstablished($key)){
                            $this->server->push($key,json_encode($data));
                        }
                    }
                    break;
                case 'notice_user':
                    if($this->server->isEstablished($data['fd'])){
                        $this->server->push($data['fd'],json_encode($data['data']));
                    }
                    break;
                default:
                    break;
            }
//            $serv->finish($data);
        });
        $this->server->on('request', function ($request, $response) {
            // 接收http请求从get获取message参数的值，给用户推送
            // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
            foreach ($this->server->connections as $fd) {
                // 需要先判断是否是正确的websocket连接，否则有可能会push失败
                if ($this->server->isEstablished($fd)) {
                    $this->server->push($fd, $request->get['message']);
                }
            }
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

                }
            });
        }
    }
    //验证用户是否可以订阅频道
    protected function vertify_user($user_fd,$user_key,$channel_name){
        $channel_name=$this->config['channel_suffix'].$channel_name;//为了防止频道名与redis的键重复添加前缀
        $key=$this->redis->hGet($this->config['channel'],$channel_name);
        if($key==''||$user_key==$key){//当频道的秘钥为空（既是个公共频道）或用户提供的密码与频道对应的密码一致即可订阅改频道
            $this->redis->hSet($channel_name,$user_fd,time());//订阅当前的频道
            $user_channel_info=$this->redis->hGet($this->config['online'],$user_fd);
            $user_channel_info=json_decode($user_channel_info,true);
            $user_channel_info[]=$channel_name;
            $this->redis->hSet($this->config['online'],$user_fd,json_encode($user_channel_info));//记录当前fd订阅的频道
            return true;
        }
        return false;
    }
    //
    protected function onuserclose(){

    }
    //创建频道
    protected function create_channel($channel_name,$channel_password){
        $channel_name=$this->config['channel_suffix'].$channel_name;//为了防止频道名与redis的键重复添加前缀
        $this->redis->hSet($this->config['channel'],$channel_name,$channel_password);
    }
    //当服务器重启的时候清除掉过去的数据
    protected function onrestart(){
        foreach ($this->redis->hGetAll($this->config['channel']) as $key=>$value){
            $this->redis->del($key);
        }
        $this->redis->del($this->config['channel']);
        $this->redis->del($this->config['online']);
        $this->redis->del($this->config['user_rel_id']);
    }
}
new awesome_echo();