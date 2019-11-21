<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/24 0024
 * Time: 上午 8:38
 */

namespace task;


use system\Exception;
use task\queue\queue;

class rabbitmq
{
    protected $connection;
    protected $con_config=[
        'host' => '127.0.0.1',
        'port' => '5672',
        'login' => 'guest',
        'password' => 'guest',
        'vhost'=>'/'
    ];//connection config
    protected static $queue_object=[];
    protected static $exchage_object=[];
    protected $channel;
    protected $exchange;
    public function __construct()
    {
        $this->connection = new \AMQPConnection($this->con_config);
        if (!$this->connection->connect()) {
            new Exception('500', 'cant not connect the amqp');
        }
        $this->channel=new \AMQPChannel($this->connection);
    }
    public function exchange($exchange_name,$queue_name,$route_key=''){
        $key=$exchange_name.$queue_name;
        if(array_key_exists($key,self::$exchage_object)){
            return self::$exchage_object[$key];
        }
        $exchange=new \AMQPExchange($this->channel);
        $exchange->setName($exchange_name);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();
        $this->queue($queue_name,$exchange_name,$route_key);
        self::$exchage_object[$key]=$exchange;
        return $exchange;
    }
    public function push($exchange_name,$queue_name,$message,$router_key=''){
        $this->exchange($exchange_name,$queue_name,$router_key)->publish($message,$router_key);
    }
    public function get_message_num($queue_Name,$router_key=''){
        $queue = new \AMQPQueue($this->connection);
        $queue->setFlags(AMQP_PASSIVE);
        $messageCount = $queue->declare($queue_Name);
    }
    public function get_message($queue_name){
    }
    public function queue($queue_name,$exchage_name,$route_key=''){
        $key=$queue_name.$exchage_name.$route_key;
        if(array_key_exists($key,self::$queue_object)){
            return self::$queue_object[$key];
        }
        $queue=new \AMQPQueue($this->channel);
        $queue->setName($queue_name);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $queue->bind($exchage_name,$route_key);
        self::$queue_object[$key]=$queue;
        return $queue;
    }
    public function block_handle($exchage_name,$queue_name,$route_key='',\Closure $fuc){
        if(!is_cli()){
            new Exception('500','block handle way can not use in fpm');
        }
        $queue=$this->queue($queue_name,$exchage_name,$route_key);
        $queue->consume($fuc,AMQP_AUTOACK);
    }
    public function get($exchage_name,$queue_name,$route_key=''){
        $queue=$this->queue($queue_name,$exchage_name,$route_key);
        $data=$queue->get(AMQP_AUTOACK);
        if($data){
            return $data->getBody();
        }
        return null;
    }
    public function handle($envelope, $queue){
        
    }
}