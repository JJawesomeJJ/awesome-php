<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/30 0030
 * Time: 下午 10:22
 */

namespace system\config;


class service_config extends service
{
    public function service()
    {
        self::add_service_config("timed_task",self::$home_path."/task/timed_task/timed_task.php");
        self::add_service_config("websocket_chat",self::$home_path."/websocket_plus.php");
    }
}