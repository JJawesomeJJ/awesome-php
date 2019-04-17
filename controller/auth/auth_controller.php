<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/16 0016
 * Time: 下午 2:29
 */
namespace controller\auth;
use request\request;
use controller\controller;
use db\db;
use system\file;
use system\queue;
use system\template;
use system\token;
use system\Exception;
use system\mail;
use system\vertify_code;
use task\add_task;
use task\task;

class auth_controller extends controller
{
    public function user_login()
    {
        session_start();
        $rules=[
            "name"=>"required:post",
            "password"=>"required:post|min:1|max:100",
            "code"=>"required:post"
        ];
        $request=new request($rules);
        $db=new db();
        $name=$request->get("name");
        $result=$db->query("user",["password","email","head_img"],"name='$name'");
        if(strtolower($_SESSION['code'])<>strtolower($request->get("code")))
        {
            return ["code"=>"403","msg"=>"fail","data"=>"code_error"];
        }
        if($result==""){
            $arr = array("code" => "404", "msg" => "fail", "data" =>"unsign");
        }
        if($result["password"]==$request->get("password"))
        {
            $_SESSION['email']=$result["email"];
            $_SESSION['user']=$name;
            $token=new token();
            $token->set_token($name,$result["email"]);
            return["code" => "200", "msg" => "suceess", "data" => "ok","email"=>$result["email"],"head_img"=>$result["head_img"]];
        }
        else{
            return ["code" => "404", "msg" => "fail", "data" => "password_error"];
        }
    }
    public function logout(){
        session_start();
        setcookie("PHPSESSID",session_id(),time()-3600,'/',$_SERVER['HTTP_HOST'],false,false);
        $token=new token();
        $token->delete_token($_COOKIE["user_token"]);
    }
    public function user_register(){
        $rules=[
            "name"=>"required:post|min:4|max:20|unique:user",
            "password"=>"required:post|min:6|max:225",
            "sex"=>"required:post|accept:man,women",
            "code"=>"required:post",
            "email"=>"required:post|email:ture|unique:user"
        ];
        $request=new request($rules);
        session_start();
        if($_SESSION['code']==$request->get("code"))
        {
            $url="";
            if($request->get("sex")=="man"){
                $man_json_list=["/var/www/html/head_img_src/头像男生帅气背影.json",
                    "/var/www/html/head_img_src/欧美头像男.json"
                ];
                $file_name=$man_json_list[array_rand($man_json_list,1)];
                $file=fopen(@$file_name,"r");
                $head_list=json_decode(fread($file,filesize($file_name)));
                $url=$head_list[array_rand($head_list)];
            }
            else{
                $man_json_list=["/var/www/html/head_img_src/女生可爱头像.json",
                    "/var/www/html/head_img_src/欧美头像女.json"
                ];
                $file_name=$man_json_list[array_rand($man_json_list,1)];
                $file=fopen(@$file_name,"r");
                $head_list=json_decode(fread($file,filesize($file_name)));
                $url=$head_list[array_rand($head_list)];
            }
            $arr=["name"=>$request->get("name"),"password"=>$request->get("password"),"email"=>$request->get("email"),"sex"=>$request->get("sex"),"head_img"=>$url];
            $db=new db();
            $db->insert_databse("user",$arr);
            $_SESSION['user']=$request->get("name");
            $_SESSION['email']=$request->get("email");
            $token=new token();
            $token->set_token("user",$request->get("email"));
            return ["code" => "200", "msg" => "suceess", "head_img" => $url,"email"=>$request->get("email")];
        }
        else{
            return ["code"=>"403","msg"=>"fail","data"=>"code_error"];
        }
    }
    public function auth_login(){
        $rules=[
            "name"=>"required:get",
            "password"=>"required:get",
            "code"=>"required:get"
        ];
        $request=new request($rules);
        if(strtolower($request->get("code"))!=strtolower($_SESSION['code']))
        {
            return ['code'=>'400','message'=>'code_error'];
        }
        $db=new db();
        $name=$request->get('name');
        $result=$db->query("admin_user",['password','permission'],"name='$name'");
        return $result;
    }
    public function auth($name,$store_name=false,$parms_list=false){
        if(!isset($_SESSION))
        {
            session_start();
        }
        if(isset($_SESSION[$name]))
        {
            return $_SESSION[$name];
        }
        else{
            $token=new token();
            if(func_num_args()==2)
            {
                if($token->check_token($store_name)==true)
                {
                    return $_SESSION[$name];
                }
            }
            if(func_num_args()==3)
            {
                if($token->check_token($store_name,$parms_list)==true)
                {
                    return $_SESSION[$name];
                }
            }
            if($token->check_token()==true)
            {
                return $_SESSION[$name];
            }
            else{
                new Exception("405","relogin");
            }
        }
    }
    public function admin_login(){//管理员登录
        session_start();
        $rules=[
            "name"=>"required:get",
            "password"=>"required:get",
            "admin_email_code"=>"required:get",
        ];
        $request=new request($rules);
//        if($_SESSION["admin_email_code"]!=$request->get("admin_email_code"));
//        {
//            return ["code"=>"405","message"=>"code_error"];
//        }
        $db=new db();
        $name=$request->get('name');
        $result=$db->query("admin_user",['password','permission','status','email'],"name='$name'");
        if(count($result)==0)
        {
            return ["code"=>"405","message"=>"unsign"];
        }
        if($result['password']==$request->get('password'))
        {
            $token=new token();
            $this->auth("admin_user","admin_list",["admin_user"=>"name","permission"=>"permission"]);
            //$token->set_token($name,$result['email'],'admin_list',['permission'=>$result['permission']]);
            return ["code"=>"200","message"=>"ok"];
        }
        else{
            return ["code"=>"405","message"=>"password_error"];
        }
    }
    public function request_connect_websocket(){
        $queue=new queue();
        $redis=$queue->redis;
        if($this->auth("user")!=null)//授权用户是否可以登录websocket服务器
        {
            $server_token=md5(time().$this->auth('user'));
            $token=["token_value"=>$server_token,"expire"=>"604800"];
            $redis->hSet("users",$this->auth("user"),json_encode($token));
            return ["code"=>"200","server_token"=>$server_token];
        }
    }
    public function get_head_img(){
        $rules=[
            "name"=>"required:get"
        ];
        $request=new request($rules);
        $name=$request->get("name");
        $db=new db();
        $result=$db->query("user",["head_img"],"name='$name'");
        return $result;
    }
}