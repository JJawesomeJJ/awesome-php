<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: 2020-01-26 14:01:03
 */
namespace app\EventListener;
use app\Event\native_push_event;
use system\kernel\event\EventListener;

class native_push_listener extends EventListener
{
    public function handle(native_push_event $event)
    {
        
    }
}