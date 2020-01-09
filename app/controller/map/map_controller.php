<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28 0028
 * Time: 下午 10:21
 */
namespace app\controller\map;
use app\controller\auth\auth_controller;
use app\controller\controller;
use db\db;
use db\model\model;
use db\model\model_auto\model_auto;
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
        $table_name=$location_info["addressComponent"]["province"];
        $map=model_auto::model($table_name);
        $arr=[
            "id"=>md5($key.$request->get("tele")),
            "city"=>$location_info["addressComponent"]["city"],
            "road"=>$location_info["addressComponent"]["street"],
            "tele"=>$request->get("tele"),
            "owner_info"=>$request->get("owner_info"),
            "distrist"=>$location_info["addressComponent"]["district"],
            "latitude_longitude"=>$location_info["location"]["lat"].','.$location_info["location"]["lng"],
            "contacts"=>$request->get("contacts"),
            "writer"=>auth_controller::auth("user")
        ];
        $map->create($arr);
//        return [
//            "code"=>200,
//            "message"=>"ok"
//        ];
        $task=new add_task();
        $task->add("store","store_base64",["base64"=>str_replace("*","+",$request->get("img1")),"path"=>"/var/www/html/image/","name"=>$arr["id"]."_1"]);
        $task->add("store","store_base64",["base64"=>str_replace("*","+",$request->get("img2")),"path"=>"/var/www/html/image/","name"=>$arr["id"]."_2"]);
        $task->add("store","store_base64",["base64"=>str_replace("*","+",$request->get("img3")),"path"=>"/var/www/html/image/","name"=>$arr["id"]."_3"]);
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
        $request=new request();
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
    public function location_to_city(request $request){
        $location=$this->coordinate_city($request->get("latitude"),$request->get("longtitude"));
        return $this->get_user_park_by_location($location["province"],$location["city"]);
    }
    public function map_message_manage(){
        $rules=[
            "privince"=>"required:get",
            "city"=>"required:get",
            "check_condition"=>"required:get",
            "sort"=>"required:get"
        ];
        $request=$this->request()->verifacation($rules);
        $condition="";
//        $city_condition=$this->required_condition("city",$request->get("city"));
//        $check_condition=$this->required_condition("verified",$request->get("check_condition"));
//        $condition=" $city_condition and $check_condition";
//        $result=$db->query($request->get("privince"),["id","city","road","tele","owner_info","latitude_longitude","distrist","verified","create_time","contacts","writer"],$condition);
        $result=[];
        $map=new model_auto($request->get("privince"));
        if(($city=$request->get("city"))!="全部"){
            $map->where_like("city",$request->get("city"));
        }
        if($this->request()->get("check_condition")!="全部"){
            $map->where("verified",$request->get("check_condition"));
        }
        $result=$map->get()->all();
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
//        $db=new db();
//        $condition="";
//        $city_condition=$this->required_condition("city",$request->get("city"));
//        $condition=" $city_condition and verified=1";
//        $result=$db->query($request->get("privince"),["city","road","tele","owner_info","latitude_longitude","distrist","verified","create_time","contacts"],$condition);
        $result=[];
        $map=new model_auto($request->get("privince"));
        $result=$map->where("city",$request->get("city"))->where("verified",1)->get()->all();
        return $result;
    }
    public function get_user_park_by_location($privince,$city){
        $map=model_auto::model($privince);
        $data=$map->where("city",$city)->where("verified","1")->get()->all();
        $con=count($data);
        for ($i=0;$i<$con;$i++){
            $data[$i]["privince"]=$privince;
        }
        return $data;
    }
    public function get_instance(request $request){
        $rules=[
            "origins"=>"required",
            "destinations"=>"required",
        ];
        $request->verifacation($rules);
        $http=new http();
        $user_input=$request->all();
        $user_input["ak"]="YnSwNie3HUMbA6pQba36vGX0hVux7uMA";
        return $http->get("http://api.map.baidu.com/routematrix/v2/driving",$user_input);
    }
    public function start_park(request $request){
        $rule=[
            "id"=>"required",
            "user_id"=>"reqiured"
        ];
        $request->verifacation($rule);
        if(($park=$this->cache()->get_cache($request->get("id")))==null){
            $this->cache()->set_cache($request->get("id"),[
                "start_time"=>microtime(),
                "user_id"=>$request->get("user_id")
            ],"forever");
        }
        return ["code"=>200,"message"=>"ok"];
    }
    public function stop_park(request $request){

    }
}