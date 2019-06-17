<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14 0014
 * Time: 上午 8:48
 */

namespace task\queue;


use SuperClosure\Serializer;
use system\cache\cache;
use system\cache\cache_;
use system\config\config;
use system\config\timed_task_config;
use system\Exception;
use system\system_excu;

class timed_task
{
    protected static $redis_;
    protected $redis;
    protected $task_arr=[];
    protected $task_handle_num="timed_task_handle_num";
    public function __construct()
    {
        echo "load";
        require_once __DIR__."/../../load/auto_load.php";
        if(!$this->is_cli()){
            new Exception("403","forbidden");
        }
        $this->redis=new \Redis();
        $this->redis->connect(config::redis()["host"],config::redis()["port"]);
        $this->redis->del("timed_task_worker");
        //当程序重启即认为所有在运行的定时任务均已关闭
        if($this->redis->exists("timed_task")){
            $this->task_arr=json_decode($this->redis->get("timed_task"),true);
        }//恢复数据当守护进程被关掉重启时从redis里面获取备份数据
        $cache=new cache();
        if($cache->get_cache($this->task_handle_num)==null){
            $cache->set_cache($this->task_handle_num,0,"forever");
        }
        $this->sort_by_time();
        system_excu::record_my_pid(__FILE__);
        $this->scan_task();
    }
    public static function add_closure_timed_task($task_name,\Closure $function,$time,$interval,$times="forever")
    {
        if (!is_numeric($interval)) {
            new Exception("403", "interval should be a number");
        }
        if (!$function instanceof \Closure) {
            new Exception("403", "$function shoule be a Closure");
        }
        if ($times != "forever" && !is_numeric($times)) {
            new Exception("403", "times should be 'forever or a number'");
        }
        $task_id = md5($task_name . $time);
        $time=strtotime(date("Y-m-d $time"));
        if($time==null){
            new Exception("403","the time format should in 24:00");
        }
        if (!self::exist_task($task_id)) {
            if (self::$redis_ == null) {
                self::$redis_ = new \Redis();
                self::$redis_->connect(config::redis()["host"], config::redis()["port"]);
            }
            $serilize=new Serializer();
            $function=$serilize->serialize($function);
            self::$redis_->hSet("timed_task_closure",$task_id,$function);
            self::$redis_->lPush("add_timed_task", json_encode(["task_name" => $task_name, "time" => $time, "times" => $times, "interval" => $interval, "type" => "closure", "task_id" =>$task_id]));
        }
    }
    public static function add_timed_task($task_name,$time,$interval,$times="forever"){
        if(!is_numeric($interval)){
            new Exception("403","interval should be a number");
        }
        if(!in_array($task_name,timed_task_config::timed_task_command())){
            new Exception("403","$task_name should define in timed_task_config or task_name shoule be a Closure");
        }
        if(!is_numeric($time)){
            new Exception("403","time should be a timestamp");
        }
        if($times!="forever"&&!is_numeric($times)){
            new Exception("403","times should be 'forever or a number'");
        }
        $task_id=md5($task_name.$time);
        $time=strtotime(date("Y-m-d")." ".$time);
        if($time==null){
            new Exception("403","the time format should in 24:00");
        }
        if(!self::exist_task($task_id)) {
            if (self::$redis_ == null) {
                self::$redis_ = new \Redis();
                self::$redis_->connect(config::redis()["host"], config::redis()["port"]);
            }
            self::$redis_->lPush("add_timed_task", json_encode(["task_name" => $task_name, "time" => $time, "times" => $times, "interval" => $interval, "type" => "command", "task_id" => $task_id]));
        }
    }//默认任务为永久执行，但可以设置次数
    //use task_name=> task_name and the name should define in system/config/timed_task_config
    //use $time set time to excute this task
    //use $times set process excute times
    //use $interval shoule be a number
    protected function sort_by_time(){
        array_multisort(array_column($this->task_arr,'time'),SORT_ASC,$this->task_arr);
    }
    protected function scan_task(){
        while(true){
            $this->redis->hSet(config::task_record_list()["name"]."time","timed_task",date('Y-m-d H:i:s'));
            $cache=new cache();
            foreach (timed_task_config::timed_task_schedule() as $command){
                if($cache->get_cache($command)==null||$cache->get_cache($command)!=md5_file($command)){
                    echo "$command";
                    exec("php $command" . ' > /dev/null &');
                    $cache->set_cache($command,md5_file($command),"forever");
                }
            }
            if($this->redis->lLen("add_timed_task")>0){
                while (($timed_task_data=$this->redis->lPop("add_timed_task"))!=null){
                    $this->task_arr[]=json_decode($timed_task_data,true);
                }//检测有无新增的定时任务，若有取出加入数组并备份
                $this->sort_by_time();
                //对任务进行排序
                $this->redis->set("timed_task",json_encode($this->task_arr));
                //保存任务
            }
            if(count($this->task_arr)>0) {
                $task_data = $this->task_arr[0];
                if ($task_data["time"] <= microtime(true)) {
                    $now_handle_num=$cache->get_cache($this->task_handle_num);
                    $now_handle_num=$now_handle_num+1;
                    $cache->set_cache($this->task_handle_num,$now_handle_num,"forever");
                    if ($task_data["type"] == "command") {
                        $command = timed_task_config::timed_task_command()[$task_data["task_name"]];
                        exec("php $command" . ' > /dev/null &');
                    }
                    if ($task_data["type"] == "closure") {
                        $name = $task_data["task_id"];
                        $command = timed_task_config::timed_task_command()["closure"];
                        echo "php $command $name" . ' > /dev/null &';
                        exec("php $command $name" . ' > /dev/null &');
                    }
                    if (is_numeric($this->task_arr[0]["times"])) {
                        if ($this->task_arr[0]["times"] > 0) {
                            $this->task_arr[0]["time"] = $this->task_arr[0]["time"] + $this->task_arr[0]["interval"];
                            $this->task_arr[0]["times"] = $this->task_arr[0]["times"] - 1;
                        } else {
                            array_shift($this->task_arr);
                            //当执行次数是有限的当执行完成 删除任务
                        }
                    } else {
                        $this->task_arr[0]["time"] = $this->task_arr[0]["time"] + $this->task_arr[0]["interval"];
                    }
                    $this->sort_by_time();
                    $this->redis->set("timed_task", json_encode($this->task_arr));
                    //重新排序
                }
                else{
                    print_r($this->task_arr);
                    echo "sleep60s".PHP_EOL;
                    sleep(60);
                    //没有要执行的任务休眠
                }
            }
            else{
                print_r($this->task_arr);
                echo "sleep60s".PHP_EOL;
                sleep(60);
                //没有要执行的任务休眠
            }
        }
    }
    protected static function exist_task($task_id){
        if(self::$redis_==null){
            self::$redis_=new \Redis();
            self::$redis_->connect(config::redis()["host"],config::redis()["port"]);
        }
        $task_arr=self::$redis_->get("timed_task");
        if($task_arr==null){
            return false;
        }
        else{
            $task_arr=json_decode($task_arr,true);
        }
        foreach ($task_arr as $value){
            if($value["task_id"]==$task_id){
                return true;
            }
        }
        return false;
    }
    protected function between_days_to_last_month($day_timestamps){
        $date=date("Y-m-d",$day_timestamps);
        $date_list=explode("-",$date);
        $date_list[1]=$date_list[1]+1;
        $data_1=$date_list[0]."-".$date_list[1]."-".$date_list[2];
        $second1 = strtotime($data_1);
        $second2 = strtotime($date);
        if ($second1 < $second2) {
           return ($second2-$second1)/86400;
        }
        return ($second1 - $second2)/86400;
    }
    protected function is_cli(){
        return preg_match("/cli/i", php_sapi_name()) ? true : false;
    }
}