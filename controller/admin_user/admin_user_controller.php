<?php
/**
 * Created by awesome.
 * Date: 2019-05-24 03:05:05
 */
namespace controller\admin_user;
use controller\code\code_controller;
use controller\controller;
use db\model\admin_user_new\admin_user_new;
use db\model\user\user;
use request\request;
use system\cache\cache;
use system\class_define;
use system\common;
use system\config\config;
use system\config\service_config;
use system\file;
use system\mail;
use system\system_excu;
use task\queue\queue;
use task\task;
use template\compile;
use view\view;
use system\Exception;

class admin_user_controller extends controller
{
    public function register()
    {
        $admin = new admin_user_new();
        $rules = [
            "name" => "required:get|unique:admin_user_new|max:30|min:3",
            "password" => "required:get|min:60|max:225",
            "sex" => "required:get|accept:man,women",
            "email" => "required:get|unique:admin_user_new",
        ];
        $this->request()->verifacation($rules);
        $id = md5($this->request()->get("name") . microtime());
        $head_img_src = [
            "man" => [
                "/var/www/html/head_img_src/头像男生帅气背影.json",
                "/var/www/html/head_img_src/欧美头像男.json"
            ],
            "women" => [
                "/var/www/html/head_img_src/女生可爱头像.json",
                "/var/www/html/head_img_src/欧美头像女.json"
            ]
        ];
        $file = new file();
        $head_img_src = $head_img_src[$this->request()->get("sex")];
        $head_img_src = $head_img_src[array_rand($head_img_src)];
        $head_img_src = json_decode($file->read_file($head_img_src), true);
        $head_img = $head_img_src[array_rand($head_img_src)];
        $admin->create(
            [
                "name" => $this->request()->get("name"),
                "password" => $this->request()->get("password"),
                "sex" => $this->request()->get("sex"),
                "email" => $this->request()->get("email"),
                "id" => $id,
                "head_img" => $head_img,
                "permission" => $this->request()->get("permission")
            ]
        );
        return ["code" => 200, "message" => "ok", "head_img" => $head_img, "name" => $this->request()->get("name"), "permission" => $this->request()->get("permission"), "email" => $this->request()->get("email")];
    }

    public static function permission()
    {
        $cache = make("cache");
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION["admin_permission"])) {
            if (!isset($_COOKIE['admin_token'])) {
                redirect("http://39.108.236.127/php/public/index.php/admin/user");
//                new Exception("403", "admin_token_has_been_expired");
            }
            $admin_user_info = $cache->get_cache($_COOKIE['admin_token']);
            if($admin_user_info==null){
                redirect("http://39.108.236.127/php/public/index.php/admin/user");
                return ["code"=>200,"message"=>"admin_token_has_been_expired"];
            }
            $admin_user_info=$cache->get_cache($admin_user_info["name"]);
            if ($admin_user_info == null || $admin_user_info['token'] != $_COOKIE['admin_token']) {
                $cache->delete_key($_COOKIE["admin_token"]);
                new Exception("403", "admin_token_has_been_expired");
            } else {
                $_SESSION["admin_permission"] = $admin_user_info;
            }
        }
        return $_SESSION["admin_permission"];
    }

    public function set_token($name, $permission)
    {
        $token = md5($name . microtime());
        $this->cache()->set_cache($token, ["name" => "admin" . $name], 604800);
        $this->cache()->set_cache("admin" . $name, ["permission" => $permission, "name" => $name, "token" => $token], 604800);
        setcookie("admin_token", $token, time() + 604800, "/", $_SERVER['HTTP_HOST'], false, true);
    }

    public function login()
    {
        $rule = [
            "name" => "required",
            "password" => "required",
            "code"=>"reqiured"
        ];
        if(!isset($_SESSION)){
            session_start();
        }
        if(strtolower($_SESSION["admin_user"])!=strtolower($this->request()->get("code"))){
            $compile=new compile();
            return ["code"=>403,"message"=>"邮箱验证码错误"];
        }
        $this->request()->verifacation($rule);
        $admin = new admin_user_new();
        $admin->where("name", $this->request()->get("name"))->or_where("email",$this->request()->get("name"))->get();
        if ($this->request()->get("password") == $admin->password) {
            $this->set_token($admin->name,$admin->permission);
            return
                [
                    "code" => 200,
                    "message" => "ok",
                    "name"=>$admin->name,
                    "head_img" => $admin->head_img,
                    "email" => $admin->email,
                    "permission" => $admin->permission,
                    "admin_csrf_token"=>$this->sign_csrf_token($admin->name),
                ];
        }
        else{
            return ["code"=>403,"message"=>"paasowrd_error"];
        }
    }
    public function sign_csrf_token($name){
        $token=md5($name.microtime(true));
        $this->cache()->set_cache("admin_csrf_token",$token,604800);
        return $token;
    }
    public static function check_csrf_token(){
        $cache=make("cache");
        $request=new make("request");
        if($cache->get_cache($_SESSION["admin_permission"]["name"])!=$request->get("admin_csrf_token")||$cache->get_cache($_SESSION["admin_permission"]["name"]==null)) {
            new Exception("403","CSRF_ATTACK");
        }
    }
    public function email_code_login(){
        if(!isset($_SESSION)){
            session_start();
        }
        if(!isset($_SESSION["pass"])||$_SESSION["pass"]!="ok"){
            return ["code"=>"403","message"=>"forbidden"];
        }
        $admin_user=new admin_user_new();
        $admin_user->where("name",$this->request()->get("name"))->or_where("email",$this->request()->get("name"))->get();
        $email=$admin_user->email;
        $code=code_controller::code(4,"admin_user");
        queue::asyn(function () use ($email,$code){
            $mail=new mail();
            $compile=new compile();
            $title="欢迎登录Titang管理系统";
            $content="we learn you wanna login titang controller system it is vertify code if not yourself operate don't mind it!";
            $mail->send_email($email,$compile->view("tool/email",["code"=>$code,"title"=>$title,"content"=>$content]),$title);
        });
        return ["code"=>200,"message"=>"ok"];
    }
    public function user_login(){
        $compile=new compile();
        return $compile->view("admin/user_login");
    }
    public function get_timed_task_info(){
        $redis=new \Redis();
        $redis->connect(config::redis()["host"],config::redis()["port"]);
        $time=$redis->hGet(config::task_record_list()["name"]."time","timed_task");
        $task_list=json_decode($redis->get("timed_task"),true);
        return ["time"=>$time,"timed_task_list"=>$task_list];
    }
    public function system_controller_pannel(){
        $time_list=$this->get_timed_task_info();
        $websocket_status=$this->get_service_status("websocket_chat");
        $user=new user();
        $user_list=$user->where_in("name",array_keys(class_define::redis()->hGetAll("user_list")))->get()->all(["name","head_img"]);
        view("admin/titang_controller_pannel",
            [
                "permission"=>self::permission()["permission"],
                "name"=>self::permission()["name"],
                "timed_task_time"=>$time_list["time"],
                "time_num"=>strval(count($time_list["timed_task_list"])),
                "timed_task_list"=>$time_list["timed_task_list"],
                "websocket_status"=>$websocket_status,
                "user_list"=>$user_list,
                "online_user"=>count($user_list)
            ]);
        return microtime(true)-$GLOBALS["time"];
    }
    public function get_service_status($service_name){
        if(!array_key_exists($service_name,service_config::service_config())){
            new Exception("403","service_not_exist");
        }
        if(system_excu::get_task_info($service_name)==false){
            return false;
        }
        return true;
    }
    public function get_all_service_info(){
        $service_status=[];
        foreach (service_config::service_config() as $service=>$service_path)
        {
            $service_status[$service]=$this->get_service_status($service);
        }
        return $service_status;
    }
    public function get_service_info(){
        $redis=class_define::redis();
        $service_info=service_config::service_config();
        foreach ($service_info as $key=>$value){
            $service_info[$key]=system_excu::get_task_info($key);
        }
        return $service_info;
    }
    public function start_service(){
        $service_name=$this->request()->get("service");
        if(!array_key_exists($service_name,service_config::service_config())){
            new Exception("403","service_not_exist");
        }
        if(system_excu::get_task_info($this->request()->get("service"))==false){
            system_excu::excu_asyn(service_config::service_config()[$service_name]);
            return ["code"=>200,"message"=>service_config::service_config()[$service_name]];
        }
        return ["code"=>200,"message"=>"service_has_been_start"];
    }
}