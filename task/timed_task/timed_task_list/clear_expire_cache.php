<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14 0014
 * Time: 下午 9:40
 */

namespace task\timed_task\timed_task_list;


use system\cache\cache;
use task\timed_task\timed_task_handle;
require_once __DIR__."/../../../load/auto_load.php";
class clear_expire_cache extends timed_task_handle
{
    public function start()
    {
        $cache=new cache();
        foreach ($cache->get_all() as $value){
            $value=json_decode($value,true);
            $cache->get_cache($value["key"]);
        }
    }
}
new clear_expire_cache();