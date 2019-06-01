<?php
/**
 * Created by awesome.
 * Date: 2019-04-03 04:55:54
 */
namespace controller\system;
use controller\auth\auth_controller;
use controller\controller;
use db\db;
use request\request;
use system\Exception;
use task\add_task;
use task\task;

class system_controller extends controller
{
    public function notify_user(array $notify_user_list,$message){
        $task=new add_task();
        foreach ($notify_user_list as $value)
        {
            $task->add_notify("notify_user",["user_name"=>$value,"message"=>["type"=>"alert","message"=>"new_update"]]);
        }
        return ["code"=>"200","message"=>"ok"];
    }
    public function notify_user_all(){
        $db=new db();
        $user_list=$db->query("user",["name"]);
        $arr=[];
        foreach ($user_list as $value)
        {
            $arr[]=$value["name"];
        }
        $this->notify_user($arr,"update");
    }
    public function set_titang_back(){
        if(auth_controller::auth("user")!="赵李杰"){
            new Exception("403","without of permission");
        }
        else{

        }
    }
}