<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/5 0005
 * Time: 下午 11:32
 */

namespace app\providers;


use system\kernel\event\event_system;
use system\kernel\ServiceProvider\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $event_list=[
        'app\Event\user_login_event'=>[
            'app\EventListener\user_login_listener'
        ]
    ];
    public function register()
    {
        app()->make(event_system::class)->bind_event($this->event_list);
    }
    public function boot()
    {

    }
}