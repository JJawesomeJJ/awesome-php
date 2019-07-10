<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/15 0015
 * Time: 下午 9:58
 */

namespace swoole;

use system\cache\cache;

require_once "../load/auto_load.php";
class swoole_task
{
    public $mpid=0;
    public $works=[];
    public $max_process=1;
    public $new_index=0;
    public function __construct()
    {
        try {
            swoole_set_process_name(sprintf('php-task:%s', 'master'));
            $this->mpid = posix_getpid();
            $this->run();
            $this->processWait();
        } catch (\Throwable $throwable) {
            echo $throwable->getMessage();
        }
    }
        public function run(){
        new cache();
    }
}