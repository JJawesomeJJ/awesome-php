<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/19 0019
 * Time: 上午 10:42
 */

namespace task\job;


use SuperClosure\Serializer;
use task\queue\queue_handle;
require_once dirname(dirname(__DIR__))."/load/auto_load.php";
require_once dirname(dirname(__DIR__))."/load/common.php";
class asyn_queue extends queue_handle
{
    public $listen_queue="asyn";
    public function handle(array $queue)
    {
        $serialize=new Serializer();
        $job=$serialize->unserialize($queue["data"]);
        call_user_func($job);
    }
}
new asyn_queue();