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

require_once ("../pay/aop/SignData.php");
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
$test=[[]];
echo gettype($test);