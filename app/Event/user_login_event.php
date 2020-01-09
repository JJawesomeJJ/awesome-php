<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: 2019-12-23 21:12:56
 */
namespace app\Event;
use system\kernel\Channel\Channel;
use system\kernel\event\Event;

class user_login_event extends Event
{
    public $user;
    public function __construct($user)
    {
        $this->user=$user;
        parent::__construct();
    }
    /*
    @Description the event should be broadcast 
    @params Channel($channel_name,$params)
    */
     public function ShouldBroadcast()
    {
        return new Channel('event',$this->user);
    }
}