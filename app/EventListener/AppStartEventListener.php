<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: 2021-05-30 23:05:03
 */
namespace app\EventListener;
use app\Event\AppStartEvent;
use system\kernel\event\EventListener;

class AppStartEventListener extends EventListener
{
    public function handle(AppStartEvent $event)
    {
        
    }
}