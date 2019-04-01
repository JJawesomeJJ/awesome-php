<?php
namespace task;
use task\show;
use system;
class task{
    private $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", 6379);
        spl_autoload_register(function ($class){
            $file_path=explode("php/",__DIR__)[0]."php/task/";
            $file = str_replace("\\","/",$file_path.$class . '.php');
            if (is_file($file)) {
                require_once(@$file);
            }
            else{
                $file_path=explode("php/",__DIR__)[0]."php/";
                $file = str_replace("\\","/",$file_path.$class . '.php');
                if (is_file($file)) {
                    require_once(@$file);
                }
            }
        });
    }
    public function is_cli(){
        return preg_match("/cli/i", php_sapi_name()) ? true : false;
    }
    public function start(){
        if(!$this->is_cli()){
            echo "is_not_cli";
            exit();
        }
        echo "task_on_workig".PHP_EOL;
        if ($this->redis->lLen("task_list") > 0) {
            while (strlen($value = $this->redis->lpop("task_list")) > 2) {
                $task_arr = json_decode($value,true);
                $this->LoadMethod($task_arr["controller_name"], $task_arr["method"], $task_arr["arg"]);
            }
        }
        exit();
    }
    public function LoadMethod($object,$fun,$is_arg)
    {
        $object=new \ReflectionClass($object);
        if ($object->hasMethod($fun)) {
            $tmp=$object->getMethod($fun);
            if ($tmp->ispublic()) {
                if($is_arg!=false){
                    $tmp->invoke($object->newInstance($is_arg));
                }else {
                    $tmp->invoke($object->newInstance());
                }
            } else {
                throw new \Exception("call_fun_error");
            }
        } else {
            throw new \Exception("is_not_exist_fun");
        }
    }

}
$task=new task();
$task->start();