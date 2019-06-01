<?php
/**
 * Created by awesome.
 * Date: 2019-04-04 08:55:34
 */
namespace controller\post;
use controller\auth\auth_controller;
use controller\controller;
use db\factory\soft_db;
use db\model\comment_list\comment_list;
use request\request;
use db\db;
use system\http;
use task\add_task;

class post_controller extends controller
{
    public function comment(){
        $rules= [
            "comment"=>"required:get|max:225",
            "url"=>"required:get",
        ];
        $request=$this->request()->verifacation($rules);
        $comment_list=new comment_list();
        $id=md5($this->time().auth_controller::auth('user'));
        $comment_list->create(["url"=>$request->get("url"),"reply_who"=>$request->get("url"),"user_id"=>auth_controller::auth("user"),"comment_content"=>$request->get("comment"),"reply_id"=>$id,"id"=>$id]);
        return ["code"=>200,"message"=>"ok"];
    }
    public function get_comment(){
        $rules=[
            "url"=>"required:get",
        ];
        $request=$this->request()->verifacation($rules);
//        $db=new db();
//        $url=$request->get("url");
//        $result=$db->query(false,["table_name"=>["comment_list"=>["reply_id","comment_content","user_id","time","reply_who","id"],"user"=>["head_img"]],"link"=>["comment_list"=>"user_id","user"=>"name"]],"url='$url'");
        $data=soft_db::table("comment_list")->where("url",$request->get("url"))
            ->join("user","name","user_id")
            ->select(["reply_id","comment_content","user_id","comment_list.created_at","reply_who","comment_list.id","head_img"])
            ->get();
        if(count($data)==0){
            return $data;
        }
        $comment_list=[];
        if($this->is_1_array($data)){
            $data["time"]=$data["created_at"];
            unset($data["created_at"]);
            $comment_list[]=$data;
        }else {
            foreach ($data as $value) {
                $value["time"] = $value["created_at"];
                unset($value["created_at"]);
                $comment_list[] = $value;
            }
        }
        return $comment_list;
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
        $request=$this->request()->verifacation($rules);
        $db=new db();
        $id=md5($this->time().auth_controller::auth('user'));
        $comment_list=new comment_list();
        $comment_list->create(["url"=>$request->get("url"),"reply_who"=>$request->get("reply_who"),"user_id"=>auth_controller::auth("user"),"comment_content"=>$request->get("comment_content"),"reply_id"=>$request->get("reply_id"),"id"=>$id]);
//        $db->insert_databse("comment_list",["url"=>$request->get("url"),"reply_who"=>$request->get("reply_who"),"user_id"=>auth_controller::auth("user"),"comment_content"=>$request->get("comment_content"),"time"=>$this->time(),"reply_id"=>$request->get("reply_id"),"id"=>$id]);
        $task=new add_task();
        $task->add_notify("notify_user",["user_name"=>$request->get("reply_who"),"message"=>["type"=>"reply","message"=>["url"=>$request->get("full_url"),"who"=>$request->get("reply_who"),"reply_content"=>$request->get("comment_content"),"head_img"=>$request->get("head_img"),"reply_source"=>"新闻页面"]]]);
        return ["code"=>"200","message"=>"ok"];
    }//when use reply other user system will notify the others
    public function get_news_content(){
        $rules=[
            "url"=>"required:get|regex:news.sina.cn.*?\*cid="
        ];
        $request=$this->request()->verifacation($rules);
        $http=new http();
        $db=new db();
        $url=$request->get("url");
        $result=$db->query("new_content",["url_content"],"url_id='$url'");
        if(count($result)>0){
            return $this->jsonp($result["url_content"]);
        }
        else {
            $news_content = $http->get($request->get("url"));
            preg_match_all("/art_p\">([\s\S]*?)wx_pic/", $news_content, $matchs, PREG_SET_ORDER);//匹配该表所用的正则
            $content = str_replace(['\n', '\t', 'art_p">', '<p class="', '</p>', '</a>', '<div id=\'wx_pic', '<a href="JavaScript:void(0)">
', '\r'], '', $matchs[0][0]);
            $content = str_replace('none', 'block', $content);
            $content = preg_replace("/<a href=([\s\S])*?>/", "", $content);//$con= preg_replace("/<figure([\s\S])*?<\/figure>/","",$con);
            $db->insert_databse("new_content",["url_content"=>$content,"url_id"=>$url,"time"=>$this->time()]);
            return $this->jsonp($content);
        }
    }
}