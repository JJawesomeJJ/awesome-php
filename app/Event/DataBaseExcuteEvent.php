<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: 2021-08-13 17:08:10
 */
namespace app\Event;
use system\kernel\Channel\Channel;
use system\kernel\event\Event;

class DataBaseExcuteEvent extends Event
{
    public $sql;
    public $runTime;
    public $exception;
    public function __construct(string $sql,float $runTime,$exception=null)
    {
        $this->sql = $sql;
        $this->runTime = $runTime;
        $this->exception = $exception;
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