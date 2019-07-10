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
    private $home_path;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", 6379);
        $this->home_path=dirname(__FILE__)."/";
    }
    public function add($controller_name,$method,$is_arg){
        if($this->redis->lLen("task_list")==0)
        {
            exec("php $this->home_path"."task.php".' > /dev/null &');
        }
//        echo "php $this->home_path"."task.php".' > /dev/null &';
        $arr=["controller_name"=>$controller_name,"method"=>$method,"arg"=>$is_arg];
        $this->redis->rPush("task_list",json_encode($arr));
    }//add task of queue the method will judge whether start handle task process
    public function add_notify($handle_type,$params){
        $this->redis->lPush("notify_list",json_encode(["handle_type"=>$handle_type,"handle_params"=>$params]));
        if($this->redis->lLen("notify_list")==1)
        {
            exec("node $this->home_path"."/node/rouser.js".' > /dev/null &');//if the length of notify_list equal 1,start the 'rouser' process to tell the server handle task
        }
    }
}