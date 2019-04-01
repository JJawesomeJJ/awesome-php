<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/19 0019
 * Time: 下午 9:09
 */

namespace task;


class add_task
{
    private $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", 6379);
    }
    public function add($controller_name,$method,$is_arg){
        if($this->redis->lLen("task_list")==0)
        {
            exec("php /var/www/html/php/task/task.php".' > /dev/null &');
        }
        $arr=["controller_name"=>$controller_name,"method"=>$method,"arg"=>$is_arg];
        $this->redis->rPush("task_list",json_encode($arr));
    }
}