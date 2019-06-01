<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/23 0023
 * Time: 下午 8:11
 */

namespace task\timed_task\timed_task_list;


use SuperClosure\Serializer;
use system\config\config;
use task\timed_task\timed_task_handle;
require_once __DIR__."/../../../load/auto_load.php";
class closure_task extends timed_task_handle
{
    public function start()
    {
        $argv=$_SERVER["argv"];
        $serilize=new Serializer();
        $function=$serilize->unserialize($this->redis->hGet("timed_task_closure",$argv[1]));
        call_user_func($function);
    }
}
new closure_task();