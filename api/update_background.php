<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/16 0016
 * Time: 下午 10:27
 */
/*set_theme*/
/*get_index_back*/
class update_background
{
    public $redis;
    function  __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect("127.0.0.1",6379);
    }
    public function set_index_picture(){
        if(isset($_POST["list"])) {
            $this->redis->set("index_back_list",$_POST["list"]);
            $arr=array(
                "code"=>"200",
                "message"=>"ok"
            );
            echo json_encode($arr);
        }
        else{
            $arr=array(
                "code"=>"403",
                "message"=>"illegal_request"
            );
            echo json_encode($arr);
        }
}
public function get_index_picture(){
        $list=$this->redis->get("index_back_list");
        echo json_encode($list);
}
public function set_theme_back(){
    if($_POST["vertify"]!=".zlj19971998")
    {
        $arr=array(
            "code"=>$_POST["vertify"],
            "message"=>"vertify_error"
        );
        echo json_encode($arr);
        return;
    }
    if(isset($_POST["list"])) {
        $this->redis->set("theme_back_list",$_POST["list"]);
        $this->redis->set("theme_version",date("Y-m-d H:i:s"));
        $arr=array(
            "code"=>"200",
            "message"=>"ok"
        );
        echo json_encode($arr);
    }
    else{
        $arr=array(
            "code"=>"403",
            "message"=>"illegal_request"
        );
        echo json_encode($arr);
    }
}
public function get_theme_back(){
    $list=$this->redis->get("theme_back_list");
    $version=$this->redis->get("theme_version");
    $arr=array(
        "list"=>$list,
        "version"=>$version
    );
    $call = sprintf("callback(%s);", json_encode($arr));
    echo $call;
}
public function get_theme_version(){
    $version=$this->redis->get("theme_version");
    $arr=array(
        "version"=>$version
    );
    $call = sprintf("callback(%s);", json_encode($arr));
    echo $call;
}
}
$change=new update_background();
if(isset($_REQUEST["type"])) {
    $type = $_REQUEST["type"];
    switch ($type) {
        case "get_theme_back":
            $change->get_theme_back();
            break;
        case "get_index_list":
            $change->get_theme_back();
            break;
        case "update":
            $change->set_theme_back();
            break;
        case "get_version":
            $change->get_theme_version();
            break;
        default:
            $arr = array(
                "code" => "403",
                "message" => "illegal_request"
            );
            echo json_encode($arr);
            break;
    }
}
else{
    $arr=array(
        "code"=>"403",
        "message"=>"illegal_request"
    );
    echo json_encode($arr);
}