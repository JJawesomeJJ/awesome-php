<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: 2020-01-26 14:01:03
 */
namespace app\Event;
use system\kernel\Channel\Channel;
use system\kernel\event\Event;

class native_push_event extends Event
{
    protected $msg;
    public function __construct(array $msg)
    {
        $this->msg=$msg;
        parent::__construct();
    }
    /*
    @Description the event should be broadcast 
    @params Channel($channel_name,$params)
    */
     public function ShouldBroadcast()
    {
        if(!isset($this->msg['handle'])){
            $this->msg['handle']='barrage';
        }
        return new Channel($this->msg['channel_name'],json_encode($this->msg));
    }
}