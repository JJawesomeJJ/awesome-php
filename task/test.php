<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/21 0021
 * Time: 下午 9:12
 */

//namespace task;
//use db\db;
//use system\file;

require_once ("../load/auto_load.php");
class test
{
    private $redis;
    private $home_path;
    public function __construct()
    {
        $this->home_path=dirname(__FILE__)."/";
        require_once ("../load/auto_load.php");
//        $locale='en_US.UTF-8';  // 或  $locale='zh_CN.UTF-8';
//        setlocale(LC_ALL,$locale);
//        putenv('LC_ALL='.$locale);
//        $set_charset = 'export LANG=en_US.UTF-8;';
//        $a = shell_exec( $set_charset."python3 /var/www/html/php/get_head_img_url.py 美女");
//        echo $a;
        //echo "php $this->home_path"."task.php".' > /dev/null &';
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", 6379);
        //$this->get_data();
        //$db=new db();
        //$db->query(["table_name"=>["comment_list","user"],"link"=>["user_id","name"]],["head_img,user_id,comment_content"],"user_id='赵李杰'");
        //$db->query(false,["table_name"=>["comment_list"=>["user_id","comment_content"],"user"=>["head_img"]],"link"=>["comment_list"=>"user_id","user"=>"name"]]);
    }
    public function get_class_contruct_params($class_name)
    {
        $params_list = [];
        $object = new \ReflectionClass($class_name);
        $construct=$object->getConstructor();
        if($construct==null){
            return [];
        }
        $params = $construct->getParameters();
        foreach ($params as $value) {
            $params_list[] = $value->getClass()->getName();
        }
        return $params_list;
    }
    public function get_data(){
        //$this->redis->hDel("wait_notify_list");
        while($this->redis->lLen("notify_list")>0)
        {
            print_r($this->redis->lPop("notify_list"));
        }
        print_r($this->redis->hGetAll("wait_notify_list"));
    }
    public function test1($a){
        echo $a;
    }
}
class testjj{
    public function test(){

    }
}
class test1{
    public function __construct()
    {
        echo "test1";
    }
}
class test2{
    public function __construct(test1 $test,test $test1)
    {
        echo "i am test2";
    }
    public function xx(){
//        echo "i am ok";
//        return $this;
    }
    public function tt($arr){
        call_user_func($arr);
        echo strval($arr);
    }
    public function test_params(...$arr){
        echo json_encode($arr);
    }
}
class st_{
    public function __construct()
    {
        echo "load";
    }
    public static function test(){
        echo "jingtai";
    }
}
//$test=new test();
//$data=json_encode(serialize($test));
//$compile=new \template\compile();
//$compile->view("test",[]);
//$test1=new test();
//print_r($test1->get_class_contruct_params("test2"));
//call_user_func_array([$test1,"test1"],["ds"]);
//$test2=new test2();
//$test2->test_params("dsd","ds","ds");
//$test2->tt(function (){
//    echo "load";
//    return "test";
//});
//$data=["time"=>"sds","object"=>$test2];
//$store=serialize($data);
//$get_data=unserialize($store);
//$object=$get_data["object"];
//$time=time();
//echo $time."</br>";
//sleep(2);
//echo "</br>";
//echo time()."</br>";
//echo time()-$time;
//$object->xx();
//new \SignData();
//echo $_SERVER['REMOTE_ADDR'];
//new test();
////
//$test=["sdf"=>"sdf","test"=>"fsdf",["test"=>"fsd"]];
//if (count($test) == count($test, 1)) {
//    echo '是一维数组';
//} else {
//    echo '不是一维数组';
//}
//$todaytime=microtime(true);
//function between_days_to_last_month($day_timestamps){
//    $date=date("Y-m-d",$day_timestamps);
//    $date_list=explode("-",$date);
//    $date_list[1]=$date_list[1]+1;
//    $data_1=$date_list[0]."-".$date_list[1]."-".$date_list[2];
//    $second1 = strtotime($data_1);
//    $second2 = strtotime($date);
//    if ($second1 < $second2) {
//        return ($second2-$second1)/86400;
//    }
//    return ($second1 - $second2) / 86400;
//}
//echo between_days_to_last_month(microtime(true));
//$process_info=shell_exec("ps -aux | grep 4785");
//echo $process_info."<br>";
////$process_info="root 2623 0.0 1.4 302904 29416 ? S 10:39 0:00 php timed_task.php www-data 4469 0.0 0.0 4628 796 ? S 20:12 0:00 sh -c ps -aux | grep 2623 www-data 4471 0.0 0.0 11464 1000 ? S 20:12 0:00 grep 2623";
//preg_match_all("/php (.*?).php/",$process_info,$process_name,PREG_SET_ORDER);
//preg_match_all("/S  ([0-9|:| ]*?) [a-zA-Z]/",$process_info,$time_info,PREG_SET_ORDER);
//if(!isset($process_name[0][1])){
//    echo "process has been killed";
//}
//if(!isset($time_info[2][0])){
//    echo "process has been killed";
//}
//$da=exec("php /var/www/html/php/task/test2.php " . ' > /dev/null &',$out,$d);
//print_r($da);
//echo basename(__FILE__,".php");
//echo getmypid();
\system\system_excu::record_my_pid();