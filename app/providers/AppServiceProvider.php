<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/5 0005
 * Time: 下午 10:56
 */
namespace app\providers;
use db\factory\SqlRouter;
use extend\awesome\awesome_driver_rabbitmq;
use extend\awesome\awesome_echo_tool;
use request\request;
use system\class_define;
use system\config\config;
use system\kernel\event\event_system;
use system\kernel\ServiceProvider\ServiceProvider;
use system\redis;
use task\rabbitmq;

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
            return request::SingleTon();
        });
        if(class_exists(\Redis::class)) {
            app()->singleton(\Redis::class, function () {
                return class_define::redis();
            });
        }
        app()->singleton(awesome_echo_tool::class,function (){
            return new awesome_echo_tool(new awesome_driver_rabbitmq());
        });
        app()->singleton(rabbitmq::class,function (){
            return new rabbitmq();
        });
        app()->singleton(SqlRouter::class,function (){
            return SqlRouter::SingleTon(config::env_path()."filesystem/",make(\Redis::class));
        });
    }
}