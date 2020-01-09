<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/6 0006
 * Time: 下午 10:33
 */

namespace task\rabbitmq;

use SebastianBergmann\CodeCoverage\Report\PHP;
use system\common;
use system\config\config;
use system\file;

require_once __DIR__."/../../load/auto_load.php";
require_once __DIR__."/../../load/common.php";
class timer_task extends consumer
{
    protected $queue_name='timer';
    protected $exchage_name='timer';
    public function handle($msg)
    {
        $path=config::env_path()."/filesystem/test_path";
        if(!is_dir($path)){
            mkdir($path);
        }
        $file=new file();
        $file->write_file($path."/".$msg.".txt",time());
        echo $msg.PHP_EOL;
    }
}
new timer_task();