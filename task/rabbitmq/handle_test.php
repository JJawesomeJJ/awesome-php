<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/11/26 0026
 * Time: 下午 9:58
 */

namespace task\rabbitmq;

use system\Exception;

require_once __DIR__."/../../load/auto_load.php";
require_once __DIR__."/../../load/common.php";
class handle_test extends consumer
{
    protected $exchage_name='test';
    protected $queue_name='test';
    public function handle($msg)
    {
        echo $msg.PHP_EOL;
        // TODO: Implement handle() method.
    }
}
new handle_test();