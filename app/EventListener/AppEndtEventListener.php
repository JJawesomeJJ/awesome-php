<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: 2021-05-30 23:05:03
 */
namespace app\EventListener;
use app\Event\AppEndEvent;
use system\kernel\event\EventListener;

class AppEndtEventListener extends EventListener
{
    public function handle(AppEndEvent $event)
    {
        
    }
}