<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/21 0021
 * Time: 下午 9:12
 */

namespace task;
use db\db;
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
    public function get_data(){
        //$this->redis->hDel("wait_notify_list");
        while($this->redis->lLen("notify_list")>0)
        {
            print_r($this->redis->lPop("notify_list"));
        }
        print_r($this->redis->hGetAll("wait_notify_list"));
    }
}
class test2{
    public function __construct()
    {
        echo "i am test2";
    }
    public function xx(){
        echo "i am ok";
    }
}
$test2=new test2();
$data=["time"=>"sds","object"=>$test2];
$store=serialize($data);
$get_data=unserialize($store);
$object=$get_data["object"];
$object->xx();
//new test();