<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28 0028
 * Time: 下午 10:21
 */
namespace controller\map;
use controller\auth\auth_controller;
use controller\controller;
use db\db;
use function PHPSTORM_META\type;
use request\request;
use system\http;
use system\file;
use task\add_task;
use task\task;

class map_controller extends controller
{
    public function upload_park_message(){
        $rules=[
            "latitude"=>"required:post",
            "longitude"=>"required:post",
            "tele"=>"required:post",
            "contacts"=>"required:post",
            "owner_info"=>"required:post",
            "img1"=>"required:post",
            "img2"=>"required:post",
            "img3"=>"required:post"
        ];
        $request=$this->request()->verifacation($rules);
        $http=new http();
        $key=$request->get("latitude").','.$request->get("longitude");
        $location_info=json_decode($http->get("http://api.map.baidu.com/geocoder/v2/?callback=&location=$key&output=json&pois=1&ak=4nthVhrx2bl2m8bciabolGutzg44OI3Q"),true)["result"];
        $db=new db();
        $arr=[
            "id"=>md5($key+$request->get("tele")),
            "city"=>$location_info["addressComponent"]["city"],
            "road"=>$location_info["addressComponent"]["street"],
            "tele"=>$request->get("tele"),
            "owner_info"=>$request->get("owner_info"),
            "distrist"=>$location_info["addressComponent"]["district"],
            "latitude_longitude"=>$location_info["location"]["lat"].','.$location_info["location"]["lng"],
            "contacts"=>$request->get("contacts"),
            "create_time"=>date("Y-m-d H:i:s"),
            "writer"=>auth_controller::auth("user")
        ];
        $task=new add_task();
        $task->add("store","store_base64",["base64"=>str_replace("*","+",$request->get("img1")),"path"=>"/var/www/html/image/","name"=>$arr["id"]."_1"]);
        $task->add("store","store_base64",["base64"=>str_replace("*","+",$request->get("img2")),"path"=>"/var/www/html/image/","name"=>$arr["id"]."_2"]);
        $task->add("store","store_base64",["base64"=>str_replace("*","+",$request->get("img3")),"path"=>"/var/www/html/image/","name"=>$arr["id"]."_3"]);
        $db->insert_databse($location_info["addressComponent"]["province"],$arr);
        return [
            "code"=>"200",
            "message"=>"wait_vertify",
            "id"=>$arr["id"]
        ];
    }
    public function adress_to_coordinate(){
        $reules=[
            "adress"=>"required:get"
        ];
        $request=new request($reules);
        $adress=$request->get("adress");
        $url="http://api.map.baidu.com/geocoder/v2/?address=$adress&output=json&ak=4nthVhrx2bl2m8bciabolGutzg44OI3Q&callback=";
        $respone=json_decode(http::get("$url"),true);
        if(isset($_REQUEST['is_detail'])>0)//获取具体位置调用百度api
        {
            $respone_city=$this->coordinate_city($respone["result"]["location"]["lat"],$respone["result"]["location"]["lng"]);
            return [
                "lng"=>$respone["result"]["location"]["lng"],
                "lat"=>$respone["result"]["location"]["lat"],
                "privince"=>$respone_city["province"],
                "city"=>$respone_city["city"]
            ];
        }
        $respone_city=$this->coordinate_city($respone["result"]["location"]["lat"],$respone["result"]["location"]["lng"]);
        return [
            "lng"=>$respone["result"]["location"]["lng"],
            "lat"=>$respone["result"]["location"]["lat"],
        ];
    }
    public function coordinate_city($latitude,$longitude){
        $url="http://api.map.baidu.com/geocoder/v2/?callback=&location=$latitude,$longitude&output=json&pois=1&ak=4nthVhrx2bl2m8bciabolGutzg44OI3Q";
        return $respone=json_decode(http::get("$url"),true)["result"]["addressComponent"];
    }
    public function map_message_manage(){
        $rules=[
            "privince"=>"required:get",
            "city"=>"required:get",
            "check_condition"=>"required:get",
            "sort"=>"required:get"
        ];
        $request=$this->request()->verifacation($rules);
        $db=new db();
        $condition="";
        $city_condition=$this->required_condition("city",$request->get("city"));
        $check_condition=$this->required_condition("verified",$request->get("check_condition"));
        $condition=" $city_condition and $check_condition";
        $result=$db->query($request->get("privince"),["id","city","road","tele","owner_info","latitude_longitude","distrist","verified","create_time","contacts","writer"],$condition);
        return $result;
    }
    public function required_condition($name,$condition){
        if($name=='city'&&$condition!="全部")
        {
            return "city like '%$condition%'";
        }
        if($condition=="全部")
        {
            return "1";
        }
        else{
            return "$name='$condition'";
        }
    }
    public function check_pass(){
        $rule=[
            "is_pass"=>"required:get",
            "id"=>"required:get",
            "privince"=>"required:get"
        ];
        $request=$this->request()->verifacation($rule);
        $db=new db();
        $id=$request->get("id");
        if($request->get("is_pass")=="true")
        {
            echo $request->get("is_pass");
            $db->update_table($request->get("privince"),["verified"=>"1"],"where id='$id'",false);
        }
        else{
            $db->update_table($request->get("privince"),["verified"=>"2"],"where id='$id'",false);
        }
    }
    public function user_get_park(){
        $rules=[
            "privince"=>"required:get",
            "city"=>"required:get",
        ];
        $request=$this->request()->verifacation($rules);
        $db=new db();
        $condition="";
        $city_condition=$this->required_condition("city",$request->get("city"));
        $condition=" $city_condition and verified=1";
        $result=$db->query($request->get("privince"),["city","road","tele","owner_info","latitude_longitude","distrist","verified","create_time","contacts"],$condition);
        return $result;
    }
}