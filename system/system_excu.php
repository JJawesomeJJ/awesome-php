<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/30 0030
 * Time: 上午 10:48
 */
namespace system;


use load\provider;
use system\config\config;
use system\config\service_config;
use task\queue\queue_handle;

class system_excu
{
    protected $php_process_recored;
    protected static $excu_object;
    protected $redis;
    public function __construct()
    {
        $this->php_process_recored=config::task_record_list()["name"];
        $this->redis=class_define::redis();
    }
    public static function excu_asyn($command){
        if(self::$excu_object==null) {
            self::$excu_object = new system_excu();
        }
        self::$excu_object->excu($command);
    }
    public static function kill_task($task_name,$signal="-15"){
        $pid=self::get_task_pid($task_name);
        if($pid==null){
            return false;
        }else{
            $flag=false;
            $pid_list=explode('_',$pid);
            foreach($pid_list as $pid){
                if(basename(self::get_pid_php_script_name($pid))==$task_name){
                    $flag=true;
                    shell_exec("kill $signal $pid");
                }
            }
            return $flag;
        }
    }
    protected function excu($command){
        exec("php $command " . ' > /dev/null &');
    }
    public static function get_php_pid_status($pid){
        $process_info=shell_exec("ps -aux | grep $pid");
//        print_r($process_info);die();
//$process_info="root 2623 0.0 1.4 302904 29416 ? S 10:39 0:00 php timed_task.php www-data 4469 0.0 0.0 4628 796 ? S 20:12 0:00 sh -c ps -aux | grep 2623 www-data 4471 0.0 0.0 11464 1000 ? S 20:12 0:00 grep 2623";
        preg_match_all("/php (.*?)$/is",$process_info,$process_name,PREG_SET_ORDER);
        preg_match_all("/S  ([0-9|:| ]*?) [a-zA-Z]/",$process_info,$time_info,PREG_SET_ORDER);
        if(!isset($process_name[0][1])){
            return false;
        }
//        if(!isset($time_info[2][0])){
//            return false;
//        }
        return true;
    }
    public static function get_pid_php_script_name($pid){
        $process_info=shell_exec("ps -aux | grep $pid");
        preg_match_all("/php (.*?)\\.php/",$process_info,$process_name,PREG_SET_ORDER);
//        print_r($process_name);
        if(!isset($process_name[0][1])){
            return false;
        }
        else{
            return $process_name[0][1];
        }
    }
    public function restart_task($task_name,$command,$pid){
        shell_exec("kill-9 $pid");
        self::excu_asyn($task_name,$command);
    }
    public static function get_service_info(){
        $redis=class_define::redis();
        $redis->hgetall(config::task_record_list()["name"]);
    }
    public static function get_process_status($task_name){
        $pid=self::get_task_pid($task_name);
        if(is_null($pid)){
            return false;
        }
        if(strpos($pid,'_')!==false){
            $pid_list=explode('_',$pid);
            foreach ($pid_list as $pid){
                if(self::get_php_pid_status($pid)==false){
                    return false;
                }
            }
            return true;
        }
        return self::get_php_pid_status($pid);
    }
    public static function get_task_pid($task_name){
        $info=class_define::redis()->hGet(config::task_record_list()["name"],$task_name);
        if(is_null($info)){
            return null;
        }
        else{
            return json_decode($info,true)["pid"];
        }
    }
    public static function record_my_pid($path,$pid=false){
        $script_name=basename($path,".php");
        $script_pid=getmypid();
        if($pid!=false){
            $script_pid=$pid;
        }
        class_define::redis()->hSet(config::task_record_list()["name"],$script_name,json_encode(["pid"=>$script_pid,"created_at"=>time()]));
    }
    public static function get_task_info($service_name){
        $service_pid=self::get_service_pid($service_name);
        return self::get_php_pid_status($service_pid);
    }
    public static function get_service_pid($service_name){
        if(!array_key_exists($service_name, service_config::service_config()))
        {
            new Exception("404","service_undefine");
        }
        $service_info=json_decode(class_define::redis()->hGet(config::task_record_list()["name"],basename(service_config::service_config()[$service_name],".php")),true);
        return $service_info["pid"];
    }
    public static function restart_service($service_name){
        $pid=class_define::redis()->hGet(config::task_record_list()["name"],basename(service_config::service_config()[$service_name],".php"));
        exec("kill -15 $pid");
        self::excu_asyn(service_config::service_config()[$service_name]);
    }
    public static function abort_service($service_name){
        $pid=self::get_service_pid($service_name);
        exec("kill -15 $pid");
    }
    public static function service_info($service_name){
        if(!array_key_exists($service_name, service_config::service_config()))
        {
            new Exception("404","service_undefine");
        }
        $service_info=json_decode(class_define::redis()->hGet(config::task_record_list()["name"],basename(service_config::service_config()[$service_name],".php")),true);
        return $service_info;
    }
}