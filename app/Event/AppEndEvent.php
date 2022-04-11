<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: 2022-04-11 16:04:06
 */
namespace app\Event;
use system\kernel\Channel\Channel;
use system\kernel\event\Event;

class AppEndEvent extends Event
{
    public function __construct()
    {
        parent::__construct();
    }
    /*
    @Description the event should be broadcast 
    @params Channel($channel_name,$params)
    */
     public function ShouldBroadcast()
    {
        return new Channel(false);
    }
}