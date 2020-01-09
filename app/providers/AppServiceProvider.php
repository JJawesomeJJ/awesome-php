<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/5 0005
 * Time: 下午 10:56
 */
namespace app\providers;
use request\request;
use system\class_define;
use system\kernel\event\event_system;
use system\kernel\ServiceProvider\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // TODO: Implement boot() method.
    }
    public function register()
    {
        app()->singleton(event_system::class,function (){
            return event_system::SingleTon();
        });
        app()->singleton(event_system::class,function (){
            return event_system::SingleTon();
        });
        app()->singleton(request::class,function (){
            return new request();
        });
        app()->singleton(\Redis::class,function (){
            return class_define::redis();
        });
    }
}