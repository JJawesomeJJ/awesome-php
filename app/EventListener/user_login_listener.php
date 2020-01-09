<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: 2019-12-23 21:12:57
 */
namespace app\EventListener;
use app\Event\user_login_event;
use system\kernel\event\EventListener;

class user_login_listener extends EventListener
{
    public function handle(user_login_event $event)
    {
        
    }
}