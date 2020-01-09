<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/16 0016
 * Time: 下午 2:29
 */
namespace app\controller\auth;
use db\model\user\user;
use request\request;
use app\controller\controller;
use db\db;
use system\cache\cache;
use system\common;
use system\config\config;
use system\file;
use system\session;
use system\template;
use system\token;
use system\Exception;
use system\mail;
use system\vertify_code;
use task\add_task;
use task\queue\queue;
use task\task;
use template\compile;
use template\compile_parse;

class auth_controller extends controller
{
    public function user_login(request $request)
    {
        $rules=[
            "name"=>"required:post",
            "password"=>"required:post|min:1|max:100",
            "code"=>"required:post"
        ];
        $request=$this->request()->verifacation($rules);
//        $db=new db();
        $name=$request->get("name");
//        $result=$db->query("user",["password","email","head_img"],"name='$name'");
        if(!session::get('code')){
            return ["code"=>"403","msg"=>"fail","data"=>"code_error"];
        }
        if(strtolower(session::get('code'))!=strtolower($request->get("code")))
        {
//            $user=new user();
//            $user->where("name",$request->get("name"))->get()->all();
            return ["code"=>"403","msg"=>"fail","data"=>"code_error"];
        }
        $user=new user();
        $result=$user->where("name",$name)->get()->find(1);
        if(empty($result)){
            $arr = array("code" => "404", "msg" => "fail", "data" =>"unsign");
            return $arr;
        }
        if($result["password"]==$request->get("password"))
        {
            session::set("name",$request->get("name"));
            session::set("user",$request->get("name"));
            session::set("email",$result["email"]);
            common::remember_me($result["id"]);
            return["code" => "200", "msg" => "suceess", "data" => "ok","email"=>$result["email"],"head_img"=>$result["head_img"],"csrf_token"=>$this->middlware("csrf_middleware")->sign_csrf_token()];
            //csrf_token client should store this value by localstorage when request client request server we will check this token if fail the server refuse this request
        }
        else{
            return ["code" => "404", "msg" => "fail", "data" => "password_error"];
        }
    }
    public function logout(){
        common::forget();
        setcookie(config::session()["name"],session_id(),time()-3600,'/',$_SERVER['HTTP_HOST'],false,false);
    }
    public function user_register(){
        $rules=[
            "name"=>"required:post|min:4|max:20|unique:user",
            "password"=>"required:post|min:6|max:225",
            "sex"=>"required:post|accept:man,women",
            "code"=>"required:post",
            "email"=>"required:post|email:ture|unique:user"
        ];
        $request=$this->request()->verifacation($rules);
        if(session::get('code')==$request->get("code"))
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
            $id=md5(microtime(true).common::rand(4));
            $arr=["name"=>$request->get("name"),"password"=>$request->get("password"),"email"=>$request->get("email"),"sex"=>$request->get("sex"),"head_img"=>$url,"id"=>$id];
            $user=new user();
            $user->create($arr);
            session::set("name",$request->get("name"));
            session::set("email",$request->get("email"));
            common::remember_me($id);
//            $token=new token();
//            $token->set_token("user",$request->get("email"));
            return ["code" => "200", "msg" => "suceess", "head_img" => $url,"email"=>$request->get("email"),"csrf_token"=>$this->sign_csrf_token($request->get("name"))];
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
        $request=$this->request()->verifacation($rules);
        if(strtolower($request->get("code"))!=strtolower(session::get('code')))
        {
            return ['code'=>'400','message'=>'code_error'];
        }
        $db=new db();
        $name=$request->get('name');
        $result=$db->query("admin_user",['password','permission'],"name='$name'");
        return $result;
    }
    public static function auth($is_die=true){
        if(!common::is_remember($is_die)){
            return false;
        }
        return session::get('name');
//        if(!isset($_SESSION))
//        {
//            session_start();
//        }
//        if(isset($_SESSION[$name]))
//        {
//            self::check_csrf_token($_SESSION[$name]);
//            return $_SESSION[$name];
//        }
//        else{
//            $token=new token();
//            if(func_num_args()==2)
//            {
//                if($token->check_token($store_name)==true)
//                {
//                    self::check_csrf_token($_SESSION[$name]);
//                    return $_SESSION[$name];
//                }
//            }
//            if(func_num_args()==3)
//            {
//                if($token->check_token($store_name,$parms_list)==true)
//                {
//                    self::check_csrf_token($_SESSION[$name]);
//                    return $_SESSION[$name];
//                }
//            }
//            if($token->check_token()==true)
//            {
//                self::check_csrf_token($_SESSION[$name]);
//                return $_SESSION[$name];
//            }
//            else{
//                new Exception("405","relogin");
//            }
//        }
    }
    public function admin_login(){//管理员登录
        session_start();
        $rules=[
            "name"=>"required:get",
            "password"=>"required:get",
            "admin_email_code"=>"required:get",
        ];
        $request=$this->request()->verifacation($rules);
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
       $redis=new \Redis();
       $redis->connect(config::redis()["host"],config::redis()["port"]);
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
        $request=$this->request()->verifacation($rules);
        $name=$request->get("name");
        $db=new db();
        $result=$db->query("user",["head_img"],"name='$name'");
        return $result;
    }
    protected function sign_csrf_token($name){
        $csrf_token=md5($this->time().$name);
        $this->cache()->set_cache($name."csrf_token",$csrf_token,2592000);
        return $csrf_token;
    }
    protected function check_csrf_token($name){
        $request=make("request");
        $cache=new cache();
        if($request->try_get("csrf_token")==false||$request->try_get("csrf_token")!=$cache->get_cache($name."csrf_token"))
        {
            new Exception("403","csrf_attack");
        }
    }
    public function reset_password(){
        if(session::get('code')<>strtolower($this->request()->get("code"))||session::get('code')==null){
            session::forget("code");
            return ["code"=>"403","message"=>"code_error"];
        }
        session::forget("code");
        $user=new user();
        $user->where("name",$this->request()->get("user_id"));
        $user->or_where("email",$this->request()->get("user_id"))->get();
        $name=$user->name;
        $token=md5($user->name.$this->time());
        $this->cache()->set_cache($user->name."reset_token",$token,108000);
        $queue=new queue();
        $mail=new mail();
        $email=$user->email;
//        $queue->push("email",["title"=>"忘记密码","url"=>index_path()."/user/reset?token=$token&name=$name","template"=>"user/reset_link","user"=>$user->email],"email");
        queue::asyn(function ()use ($mail,$email,$token,$name){
            $mail->send_email($email,view('user/reset_link',
                ["title"=>"忘记密码","url"=>index_path()."/user/reset?token=$token&name=$name","template"=>"user/reset_link","user"=>$name]),'reset');
        });
        return ["code"=>"200","message"=>"ok"];
    }
    public function update_password(){
        $name=$this->request()->get("name");
        $this->cache()->set_cache('xiajie','test',1200);
        echo $this->cache()->get_cache($name.'reset_token').PHP_EOL;
        print_r($this->cache()->get_all());
        if($this->cache()->get_cache($name."reset_token")==$this->request()->get("token")){
            $user=new user();
            $user->where("name",$name)->get();
            $user->password=$this->request()->get("password");
            $user->update();
            $this->cache()->delete_key($name."reset_token");
            header(config::index_path());
        }
        return ["code"=>"403","message"=>"token_error"];
    }
    public function reset_password_page(){
        return view("user/reset_password",["token"=>$this->request()->get("token"),"name"=>$this->request()->get("name"),"list"=>[["name"=>"赵李杰","age"=>"2"],["name"=>"php","age"=>"16"]]]);
    }
    public function user_info(){
        if(!self::auth(false)){
            return ['code'=>600,'message'=>"unlogin"];
        }else{
            return ["code"=>200,"name"=>session::get("name"),"head_img"=>session::get("head_img")];
        }
    }
}