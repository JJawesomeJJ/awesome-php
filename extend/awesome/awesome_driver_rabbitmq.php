<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/28 0028
 * Time: 上午 11:35
 */

namespace extend\awesome;


use task\rabbitmq;

class awesome_driver_rabbitmq extends awesome_echo_driver
{
    protected $rabbitmq;
    protected $config=[
        'exchange'=>'awesome_channel',
        'queue'=>'awesome_queue'
    ];
    public function __construct()
    {
        $this->rabbitmq=new rabbitmq();
    }
    public function send(array $msg)
    {
        $this->rabbitmq->push($this->config['exchange'],$this->config['queue'],json_encode($msg));
    }
}