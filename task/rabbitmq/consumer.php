<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/27 0027
 * Time: 下午 5:14
 */

namespace task\rabbitmq;

use system\Exception;
use task\rabbitmq;

require_once __DIR__."/../../load/auto_load.php";
require_once __DIR__."/../../load/common.php";
abstract class consumer
{
    protected $rabbitmq;
    protected $exchage_name;
    protected $queue_name;
    protected $route_key='';
    public function __construct()
    {
        if(is_null($this->exchage_name)){
            new Exception("500",'exchage_name should be rewrite!');
        }
        if(is_null($this->queue_name)){
            new Exception("500",'queue_name should be rewrite!');
        }
        $this->rabbitmq=new rabbitmq();
    }
    public function get($queue_name,$exchage_name,$route_key){
        if(!is_cli()){
            new Exception('500','block handle way can not use in fpm');
        }
        $queue=$this->rabbitmq->queue($queue_name,$exchage_name,$route_key);
        $queue->consume('callback',AMQP_AUTOACK);
    }
    protected function callback($envelope, $queue) {
        $msg = $envelope->getBody();
        $queue->ack($envelope->getDeliveryTag());
    }
    abstract function handle();
}