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
use db\model\model;
use db\model\model_auto\model_auto;
use db\model\news\news;
use load\provider_register;
use function PHPSTORM_META\type;
use request\request;
use db\db;
use system\class_define;
use system\common;
use system\Exception;
use system\http;
use system\session;
use task\add_task;

class post_controller extends controller
{
    protected $news_cache_key="news_info_";
    protected $news_cache_record="news_cache_record";
    public function comment(){
        $rules= [
            "comment"=>"required:get|max:225",
            "url"=>"required:get",
        ];
        $request=$this->request()->verifacation($rules);
        $id=md5($this->time().auth_controller::auth());
        $redis=class_define::redis();
        $key=$this->news_cache_key.$request->get("url");
        if(!$redis->Exists($key)){
            $data=soft_db::table("comment_list")->where("url",$request->get("url"))
                ->join("user","name","user_id")
                ->select(["reply_id","comment_content","user_id","comment_list.created_at as time","reply_who","comment_list.id","head_img","likes"])
                ->get();
           if($this->is_1_array($data)&&!empty($data)){
               $data=[$data];
           }
           foreach ($data as $item){
               $item['from']='database';
               $redis->hSet($key,$item['id'],json_encode($item));
            }
            $redis->hset($this->news_cache_record,$key,time());//记录已经添加的键
        }
        $data=["url"=>$request->get("url"),"reply_who"=>$request->get("url"),"user_id"=>auth_controller::auth("user"),"comment_content"=>$request->get("comment"),"reply_id"=>$id,"id"=>$id,"likes"=>json_encode([]),"head_img"=>session::get("head_img")];
        $redis->hSet($key,$data['id'],json_encode($data));
        return ["code"=>200,"message"=>"ok"];
    }
    public function get_comment(){
        $rules=[
            "url"=>"required:get",
        ];
        $request=$this->request()->verifacation($rules);
        $redis=class_define::redis();
        $key=$this->news_cache_key.$request->get("url");
        $user_id=auth_controller::auth(false);
        if($redis->exists($key)){
            $return_data=[];
            $data=$redis->hGetAll($key);
            foreach ($data as $item){
                $item=json_decode($item,true);
                $item['likes']=json_decode($item['likes'],true);
                if($user_id){
                    if(array_key_exists($user_id,$item['likes'])){
                        $item['is_like']=true;
                    }
                    else{
                        $item['is_like']=false;
                    }
                }
                else{
                    $item['is_like']=false;
                }
                $item['likes']=count($item['likes']);
                $return_data[]=$item;
            }
            return $return_data;
        }else
            {
            $data = soft_db::table("comment_list")->where("url", $request->get("url"))
                ->join("user", "name", "user_id")
                ->select(["reply_id", "comment_content", "user_id", "comment_list.created_at as time", "reply_who", "comment_list.id", "head_img","likes"])
                ->get();
                foreach ($data as $item){
                    if($item['likes']==''){
                        $item['likes']=json_encode([]);
                    }
                    $redis->hSet($key,$item['id'],json_encode($item));
                }
                $redis->hset($this->news_cache_record,$key,time());//记录已经添加的键
        }
        return $data;
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
        $id=md5($this->time().auth_controller::auth('user'));
        $data=["id"=>md5(microtime(true).common::rand(4)),"url"=>$request->get("url"),"reply_who"=>$request->get("reply_who"),"user_id"=>auth_controller::auth("user"),"comment_content"=>$request->get("comment_content"),"reply_id"=>$request->get("reply_id"),"likes"=>json_encode([])];
        $redis=class_define::redis();
        $data['time']=$this->time();
        $data['head_img']=$request->get("head_img");
        $redis->hSet($this->news_cache_key.$request->get("url"),$id,json_encode($data));
        $task=new add_task();
        $task->add_notify("notify_user",[
            "user_name"=>$request->get("reply_who"),
            "message"=>[
                "type"=>"reply",
                "message"=> [
                    "url"=>$request->get("full_url"),
                    "who"=>$request->get("reply_who"),
                    "reply_content"=>$request->get("comment_content"),
                    "head_img"=>$request->get("head_img"),
                    "reply_source"=>"新闻页面"
                ]
            ]
        ]);
        return ["code"=>"200","message"=>"ok"];
    }//when use reply other user system will notify the others
    public function get_news_content(){
        $news=new news();
        $rules=[
            "url"=>"required:get|regex:news.sina.cn.*?\*cid="
        ];
        $request=$this->request()->verifacation($rules);
        $news=new news();
        if($news->where("id",$request->get("url"))->exist()) {
            return $this->jsonp($news->content);
        }
        else {
            $http=new http();
            $news_content = $http->get($request->get("url"));
            preg_match_all("/art_p\">([\s\S]*?)wx_pic/", $news_content, $matchs, PREG_SET_ORDER);//匹配该表所用的正则
            $content = str_replace(['art_p\" cms-style=\"font-L\">','\n', '\t', 'art_p">', '<p class="', '</p>', '</a>', '<div id=\'wx_pic', '<a href="JavaScript:void(0)">
', '\r'], '', $matchs[0][0]);
            $content = str_replace('none', 'block', $content);
            $content = preg_replace("/<a href=([\s\S])*?>/", "", $content);//$con= preg_replace("/<figure([\s\S])*?<\/figure>/","",$con);
            $content = preg_replace("/art_p([\s\S]*?)>/", "", $content);
            $create_arr=[
                "id"=>$request->get("url"),
                "content"=>$content
            ];
            if($request->try_get("title")){
                $create_arr["title"]=$request->get("title");
            }
            $news->create([
                "id"=>$request->get("url"),
                "content"=>$content,
            ]);
            $likes=model_auto::model("news_likes");
            return $this->jsonp($content);
        }
    }
    public function likes(request $request){
        $is_like=false;
        $redis=class_define::redis();
        $key=$this->news_cache_key.$request->get("url");
        while (!$redis->setex($redis->get("id"),2,$this->time())){
            usleep(5);
        }
        if(!$data=$redis->hGet($key,$request->get("id"))){
            new Exception("404",'COMMENT NOT FIND');
        }
        $data=json_decode($data,true);
        $data['likes']=json_decode($data['likes'],true);
        $user_id=auth_controller::auth();
        if(array_key_exists($user_id,$data['likes'])){
            unset($data['likes'][$user_id]);
            $is_like=false;
        }
        else{
            $data['likes'][$user_id]=time();
            $is_like=true;
        }
        $data['likes']=json_encode($data['likes']);
        $redis->hSet($key,$request->get("id"),json_encode($data));
        $redis->del($redis->get('id'));
        return ['code'=>200,"is_like"=>$is_like];
    }
}