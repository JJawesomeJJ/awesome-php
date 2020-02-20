<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/19 0019
 * Time: 下午 9:32
 */

namespace system\kernel\event;

abstract class Event
{
    public function __construct()
    {
        if(method_exists($this,"ShouldBroadcast")) {
            $this->ShouldBroadcast();
        }
    }
    //是否通过awesome-echo 进行广播
    abstract function ShouldBroadcast();
}