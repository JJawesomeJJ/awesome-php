<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/21 0021
 * Time: 下午 11:51
 */
namespace app;
use request\request;
use system\class_define;
use system\kernel\event\event_system;

class ServiceProvider{
    public function class_factory(){
    }
    public static function event(){
        return [
            'app\Event\user_login_event'=>[
                'app\EventListener\user_login_listener'
            ]
        ];
    }
}