<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/20 0020
 * Time: 下午 8:51
 */

namespace swoole;
require_once __DIR__."/../load/auto_load.php";
$server=new native_view();
class native_view{
    private $addr="0.0.0.0";
    private $port="9501";
    public $time=0;
    public $redis;
    public $name;
    public $users=array(
    );
    function __construct(){
        $this->lock=new \swoole_lock(SWOOLE_MUTEX);
        $this->server=new \swoole_websocket_server($this->addr,$this->port);
        $this->server->set(array(
            'daemonize' => 1,
            'worker_num' => 4,
            'task_worker_num' => 10,
            'max_request' => 1000,
            'log_file' => ROOT_PATH . 'storage\\logs\\swoole.log'
        ));
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1",6379);
        $this->redis->delete("users");
        $this->server->on('open',array($this,'onopen'));
        $this->server->on('message',array($this,'onmessage'));
        $this->server->on('task', array($this, 'onTask'));
        $this->server->on('finish',array($this,'onfinish'));
        $this->server->on('close',array($this,'onclose'));
        $this->server->start();
    }
    public function onopen($server,$request){
        $this->time=$this->time+1;
        $message=array(
            'remote_addr'=>$request->server['remote_addr'],
            'request_time' => date('Y-m-d H:i:s', $request->server['request_time'])
        );
        echo "连接成功";
    }
    public function onmessage($server,$frame){
        $data=json_decode($frame->data,true);
        switch ($data["type"]){
            case "base64":
                $this->server->task(json_encode(["type"=>"base64","base64"=>$data["base64"]]));
                break;
            default:
                break;
        }
    }
    public function onTask($server,$task_id,$form_id,$message){
        foreach ($this->server->connections as $fd)
        {
            $this->server->push($fd,json_encode($message));
        }
        $server->finish('Task'.$task_id.'Finished'.PHP_EOL);
    }
    public function onclose($server,$fd){
//        $user=json_decode($this->redis->get('users'));
//        if(gettype($user)=='object'){
//            $user=get_object_vars($user);
//        }
//        unset($user[$this->name]);
//        echo $this->name.'close'.PHP_EOL;
//        $this->redis->set("users",json_encode($user));
//        $response=array(
//            'type'=>'system',
//            'online'=>$user,
//            'count'=>count($user),
//            'online_count'=>count($user),
//            'time'=>$this->time
//        );
//        //$this->server->push($frame->fd,json_encode($response));
//        $this->server->task($response);
//        $this->lock->lock();
//        $this->lock->unlock();
    }
    public function onfinish(){

    }
}
?>