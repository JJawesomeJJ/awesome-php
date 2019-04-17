<?php
/**
 * Created by awesome.
 * Date: 2019-04-04 08:55:34
 */
namespace controller\post;
use controller\auth\auth_controller;
use controller\controller;
use request\request;
use db\db;
use task\add_task;

class post_controller extends controller
{
    public function comment(){
        $rules= [
            "comment"=>"required:get|max:225",
            "url"=>"required:get",
        ];
        $request=new request($rules);
        $db=new db();
        $id=md5($this->time().auth_controller::auth('user'));
        $db->insert_databse("comment_list",["url"=>$request->get("url"),"reply_who"=>$request->get("url"),"user_id"=>auth_controller::auth("user"),"comment_content"=>$request->get("comment"),"time"=>$this->time(),"reply_id"=>$id,"id"=>$id]);
        return ["code"=>200,"message"=>"ok"];
    }
    public function get_comment(){
        $rules=[
            "url"=>"required:get",
        ];
        $request=new request($rules);
        $db=new db();
        $url=$request->get("url");
        $result=$db->query(false,["table_name"=>["comment_list"=>["reply_id","comment_content","user_id","time","reply_who","id"],"user"=>["head_img"]],"link"=>["comment_list"=>"user_id","user"=>"name"]],"url='$url'");
        return $result;
    }
    public function reply(){
        $rules=[
            "reply_who"=>"required:post",
            "comment_content"=>"required:post|max:225",
            "reply_id"=>"required:post",
            "url"=>"required:post",
            "head_img"=>"required:post",
            "full_url"=>"required:post"
        ];
        $request=new request($rules);
        $db=new db();
        $id=md5($this->time().auth_controller::auth('user'));
        $db->insert_databse("comment_list",["url"=>$request->get("url"),"reply_who"=>$request->get("reply_who"),"user_id"=>auth_controller::auth("user"),"comment_content"=>$request->get("comment_content"),"time"=>$this->time(),"reply_id"=>$request->get("reply_id"),"id"=>$id]);
        $task=new add_task();
        $task->add_notify("notify_user",["user_name"=>$request->get("reply_who"),"message"=>["type"=>"reply","message"=>["url"=>$request->get("full_url"),"who"=>$request->get("reply_who"),"reply_content"=>$request->get("comment_content"),"head_img"=>$request->get("head_img"),"reply_source"=>"新闻页面"]]]);
        return ["code"=>"200","message"=>"ok"];
    }//when use reply other user system will notify the others
}