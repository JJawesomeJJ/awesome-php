<?php
/**
 * Created by awesome.
 * Date: 2019-04-03 04:55:54
 */
namespace controller\system;
use controller\admin_user\admin_user_controller;
use controller\auth\auth_controller;
use controller\controller;
use db\db;
use db\model\model;
use db\model\model_auto\model_auto;
use db\model\user\user;
use request\request;
use system\Exception;
use task\add_task;
use task\task;
use template\compile;

class system_controller extends controller
{
    public function notify_user(array $notify_user_list,$message){
        $task=new add_task();
        foreach ($notify_user_list as $value)
        {
            $task->add_notify("notify_user", [
                "user_name"=>$value,
                "message"=>$message,
                ]);
        }
        return ["code"=>"200","message"=>"ok"];
    }
    public function notify_user_all(request $request,user $user){
        $rule = [
            "notify_way"=> "required|accept:window,text,html",
            "notify_content"=> "required|max:500|min:2",
            "title"=>"reqiured",
            "user"=>"reqired",
        ];
        $image=false;
        if($request->get_file("image")->isset_file()){
            $image=$request->get_file("image")->store_upload_file("/var/www/html/image/upload/");
        }
        $request->verifacation($rule);
        $user_arr=[];
        if($request->get("user")=="all") {
            $user_arr=$user->get()->name;
        }
        $notify_list=model_auto::model("notify_list");
        $show_message="";
        switch ($request->get("notify_way")){
            case "text":
                $show_message=$request->get("notify_content");
                break;
            case "window":
                $show_message=compile::get_tag_content("<body>","<\/body>",view("component/notify/window",[
                    "title"=>$request->get("title"),
                    "img"=>$image,
                    "content"=>$request->get("notify_content"),
                    "link"=>$request->try_get("link")
                ]));
                break;
            default:
                $show_message=$request->get("content");
                break;
        }
        $is_pass=0;
        if(admin_user_controller::permission()["permission"]=="super_admin"){
            $is_pass=1;
        }
        $notify_list->create([
            "id"=>md5(admin_user_controller::permission()["name"].microtime()),
            "publisher"=>admin_user_controller::permission()["name"],
            "recipient"=>json_encode($user_arr),
            "content"=>$request->get("notify_content"),
            "title"=>$request->get("title"),
            "notify_way"=>$request->get("notify_way"),
            "show_message"=>$show_message,
            "is_pass"=>$is_pass,
        ]);
        if(admin_user_controller::permission()["permission"]=="super_admin") {
            return $this->notify_user($user_arr, json_encode([
                "title" => $request->get("title"),
                "content" =>$show_message,
                "message_type" => "notify",
                "type" => $request->get("notify_way"),
                "image" => $image,
                "link" => $request->try_get("link"),
            ]));
        }
        else{
           return ["code"=>200,"message"=>"wait_admin_user_vertify"];
        }
    }
    public function set_titang_back(){
        if(auth_controller::auth("user")!="赵李杰"){
            new Exception("403","without of permission");
        }
        else{

        }
    }
//    public function pass_notify(request $request){
//        $notify_list=model_auto::model("notify_list")->where("id",$request->get("id"));
//        $notify_data=$notify_list->get()->all();
//        if($notify_data["notify_way"]=="window") {
//            return $this->notify_user(json_decode($notify_data["recipient"], true),)
//            }
//    }
    public function get_notify_list(request $request){
        admin_user_controller::permission();
        
    }
}