<?php
/**
 * Created by awesome.
 * Date: 2019-05-24 03:05:05
 */
namespace app\controller\admin_user;
use app\controller\code\code_controller;
use app\controller\controller;
use db\factory\migration\migration_list\migration_survey;
use db\model\admin_user_new\admin_user_new;
use db\model\model_auto\model_auto;
use db\model\user\user;
use load\provider;
use request\request;
use system\cache\cache;
use system\class_define;
use system\common;
use system\config\config;
use system\config\service_config;
use system\file;
use system\mail;
use system\session;
use system\system_excu;
use task\queue\queue;
use task\task;
use template\compile;
use system\Exception;
use template\compile_parse;

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

    public static function permission($is_redirect="/admin/user")
    {
        $cache = make("cache");
        if (!session::get("admin_permission")) {
            if (!isset($_COOKIE['admin_token'])) {
                if($is_redirect){
                redirect(index_path().$is_redirect);
                }
                else{
                    return false;
                }
//                new Exception("403", "admin_token_has_been_expired");
            }
            $admin_user_info = $cache->get_cache($_COOKIE['admin_token']);
            if($admin_user_info==null){
                if($is_redirect) {
                    redirect(index_path().$is_redirect);
                }
                else{
                    return false;
                }
                //return ["code"=>200,"message"=>"admin_token_has_been_expired"];
            }
            $admin_user_info=$cache->get_cache($admin_user_info["name"]);
            if ($admin_user_info == null || $admin_user_info['token'] != $_COOKIE['admin_token']) {
                $cache->delete_key($_COOKIE["admin_token"]);
                if($is_redirect) {
                    redirect(index_path() . $is_redirect);
                }
            } else {
                session::set("admin",(new admin_user_new())->where("name",$admin_user_info['name'])->get());
               session::set("admin_permission",$admin_user_info);
            }
        }
        return session::get("admin_permission");
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
        if(strtolower(session::get("admin_user"))!=strtolower($this->request()->get("code"))){
            $compile=new compile();
            return ["code"=>403,"message"=>"邮箱验证码错误"];
        }
        $this->request()->verifacation($rule);
        $admin = new admin_user_new();
        $admin->where("name", $this->request()->get("name"))->or_where("email",$this->request()->get("name"))->get();
        if ($this->request()->get("password") == $admin->password) {
            $this->set_token($admin->name,$admin->permission);
            session::set("admin",$admin);
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
        $this->cache()->set_cache("admin_csrf_token".$name,$token,604800);
        return $token;
    }
    public static function check_csrf_token(){
        $cache=make("cache");
        $request=make("request");
        if($cache->get_cache("admin_csrf_token".self::permission()["name"])!=$request->try_get("admin_csrf_token")) {
            new Exception("403","CSRF_ATTACK");
        }
    }
    public function email_code_login(){
        if(!session::get("pass")||session::get("pass")!="ok"){
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
        if(self::permission(false)){
            return view('admin/redirect',[
                'title'=>"欢迎你 ".session::get("admin")->name.' !',
                'btn1'=>"前往服务控制台",
                'btn2'=>'前往直播CMS',
                'path1'=>'/admin/control/websocket',
                'path2'=>'/cms/native/gift'
            ]);
        }
        return compile_parse::compile("admin/user_login",[]);
    }
    public function get_timed_task_info(){
        $redis=class_define::redis();
        $time=$redis->hGet(config::task_record_list()["name"]."time","timed_task");
        $task_list=json_decode($redis->get("timed_task"),true);
        $task_handle_num=$this->cache()->get_cache("timed_task_handle_num");
        return ["time"=>$time,"timed_task_list"=>$task_list,"timed_task_handle_num"=>$task_handle_num];
    }
    public function system_controller_pannel(request $request){
        $rules=[
            "service"=>"requred|accept:websocket,timed,titang"
        ];
        $request->verifacation($rules);
        switch ($request->get("service")){
            case "websocket":
                $time_list=$this->get_timed_task_info();
                $websocket_status=$this->get_service_status("websocket_chat");
                $user=new user();
                $user_list=$user->where_in("name",array_keys(class_define::redis()->hGetAll("user_list")))->get()->all(["name","head_img"]);
                return view("admin/websocket_service",
                    [
                        "permission"=>self::permission()["permission"],
                        "name"=>self::permission()["name"],
                        "is_active"=>$request->get("service"),
                        "timed_task_time"=>$time_list["time"],
                        "time_num"=>strval(count($time_list["timed_task_list"]??[])),
                        "timed_task_list"=>$time_list["timed_task_list"],
                        "websocket_status"=>$websocket_status,
                        "service"=>$request->get("service")."_service",
                        "user_list"=>$user_list,
                        "online_user"=>count($user_list),
                        "created"=>date('Y-m-s h:i:s',system_excu::service_info("websocket_chat")["created_at"]),
                    ]);
                break;
            case "timed":
                $time_list=$this->get_timed_task_info();
                $user=new user();
                $user_list=$user->where_in("name",array_keys(class_define::redis()->hGetAll("user_list")))->get()->all(["name","head_img"]);
                return view("admin/timed_service",
                    [
                        "permission"=>self::permission()["permission"],
                        "name"=>self::permission()["name"],
                        "timed_task_time"=>$time_list["time"],
                        "is_active"=>$request->get("service"),
                        "time_num"=>strval(count($time_list["timed_task_list"]??[])),
                        "timed_task_list"=>$time_list["timed_task_list"]??[],
                        "service"=>$request->get("service")."_service",
                        "timed_task_status"=>$this->get_service_status("timed_task"),
                        "user_list"=>$user_list,
                        "created"=>system_excu::service_info("timed_task")["created_at"],
                        "timed_task_handle_num"=>$time_list["timed_task_handle_num"]
                    ]);
                break;
            case "titang":
                $time_list=$this->get_timed_task_info();
                $user=new user();
                $user_list=$user->where_in("name",array_keys(class_define::redis()->hGetAll("user_list")))->get()->all(["name","head_img"]);
                return view("admin/titang_service",
                    [
                        "permission"=>self::permission()["permission"],
                        "name"=>self::permission()["name"],
                        "timed_task_time"=>$time_list["time"],
                        "is_active"=>$request->get("service"),
                        "time_num"=>strval(count($time_list["timed_task_list"]??[])),
                        "timed_task_list"=>$time_list["timed_task_list"],
                        "service"=>$request->get("service")."_service",
                        "timed_task_status"=>$this->get_service_status("timed_task"),
                        "user_list"=>$user_list,
                        "created"=>system_excu::service_info("timed_task")["created_at"],
                        "timed_task_handle_num"=>$time_list["timed_task_handle_num"]
                    ]);
                break;
            default:
                break;
        }
        return null;
    }
    public function get_online_user_info(){
        $user=new user();
        $user_list=$user->where_in("name",array_keys(class_define::redis()->hGetAll("user_list")))->get()->all(["id","name","head_img","sex"]);
//        $user_list=$user->where_in("name",array_keys(class_define::redis()->hGetAll("user_list")))->get()->all(["id","name","head_img","sex"]);
        return ["code"=>0,"msg"=>"","count"=>count($user_list),"data"=>$user_list];
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
        if(self::permission()["permission"]=="super_admin") {
            self::check_csrf_token();
            $service_name = $this->request()->get("service");
            if (!array_key_exists($service_name, service_config::service_config())) {
                new Exception("403", "service_not_exist");
            }
            if (system_excu::get_task_info($this->request()->get("service")) == false) {
                system_excu::excu_asyn(service_config::service_config()[$service_name]);
                return ["code" => 200, "message" => service_config::service_config()[$service_name]];
            }
            return ["code" => 200, "message" => "service_has_been_start"];
        }
        else{
            return ["code"=>403,"message"=>"without of permission"];
        }
    }
    public function restart_service(){
        if(self::permission()["permission"]=="super_admin") {
            self::check_csrf_token();
            $service_name = $this->request()->get("service");
            if (!array_key_exists($service_name, service_config::service_config())) {
                new Exception("403", "service_not_exist");
            }
            system_excu::restart_service($service_name);
            return ["code"=>200,"message"=>"ok"];
        }
        else{
            return ["code"=>403,"message"=>"without of permission"];
        }
    }
    public function abort_service(){
        if(self::permission()["permission"]=="super_admin") {
            self::check_csrf_token();
            $service_name = $this->request()->get("service");
            if (!array_key_exists($service_name, service_config::service_config())) {
                new Exception("403", "service_not_exist");
            }
            system_excu::abort_service($service_name);
            return ["code"=>200,"message"=>"service_has_been_closed"];
        }
        else{
            return ["code"=>403,"message"=>"without of permission"];
        }
    }
    public function service_switch(request $request){
        $request->verifacation([
            "operate"=>"reqired:post|accept:start,close,restart",
            "service"=>"reqired"
        ]);
        if(!array_key_exists(service_config::service_config(),$request->get("service"))){
            new Exception("404","service_not_exist");
        }
        switch ($request->get("operate")){
            case "start":
                return $this->start_service();
                break;
            case "close":
                return $this->abort_service();
                break;
            case "restart":
                return $this->restart_service();
                break;
            default:
                break;
        }
    }
    public function theme(request $request){
        $rule=[
            "type"=>"requred|accept:create,upload"
        ];
        admin_user_controller::check_csrf_token();
        admin_user_controller::permission();
        switch ($request->get("type")){
            case "create":
                $titang_theme=model_auto::model("titang_theme");
                $data=$request->get("theme_list");
                $data["creator"]=admin_user_controller::permission()["name"];
                $data["title"]=$request->get("title");
                $titang_theme->create($data,true);
                return ["code"=>200,"message"=>"ok"];
                break;
            case "update":
                $titang_theme=model_auto::model("titang_theme");
                $data=$request->get("theme_list");
                $titang_theme->where("id",$data["id"])->get()->update($data);
                return ["code"=>200,"message"=>"ok"];
                break;
            default:
                break;
        }
    }
    public function set_current_theme(request $request){
        self::check_csrf_token();
        if(self::permission()["permission"]=="super_admin"){
            $titang_theme=model_auto::model("titang_theme");
            $theme_info=$titang_theme->where("id",$request->get("id"))->get();
            $data=common::get_array_value(["morning","noon","afternoon","night"],$theme_info->find(1));
            $data["id"]=$request->get("id");
            $this->cache()->set_cache("theme_back_list",$data,"forever");
            $this->cache()->set_cache("theme_version",$theme_info->updated_at,"forever");
            return ["code"=>200,"message"=>"ok"];
        }
        else{
            return ["code"=>403,"message"=>"without_of_permission"];
        }
    }
    public function get_theme_list(request $request){
        //self::check_csrf_token();
        self::permission();
        return $titang_theme=model_auto::model("titang_theme")->page($request->get("page"),$request->get("limit"))['data'];
    }
    public function delete_theme(request $request){
        self::permission();
        if(self::permission()["permission"]=="super_admin"){
            model_auto::model("titang_theme")->where("id",$request->get("id"))->delete();
            return ["code"=>200,"message"=>"ok"];
        }
        else {
            return ["code"=>403,"message"=>"without_of_permission"];
        }
    }
    public function get_current_theme(request $request){
        switch ($request->get("type")){
            case "get_version":
                $version=$this->cache()->get_cache("theme_version");
                return $this->jsonp(["version"=>$version]);
                break;
            case "get_theme_back":
                $list=$this->cache()->get_non_exist_set("theme_back_list",function (){
                    $model=model_auto::model("titang_theme");
                    $model->find(1);
                    $data=common::get_array_value(["morning","noon","afternoon","night"],$model->all());
                    $this->cache()->set_cache("theme_version",$model->updated_at,"forever");
                    return $data;
                },"forever");
                $version=$this->cache()->get_cache("theme_version");
                if(!$request->try_get("key")){
                    $list=array_values($list);
                }
                if(!$request->try_get("withid")){
                    if(array_key_exists("id",$list)) {
                        unset($list["id"]);
                    }
                }
                $arr=array(
                    "list"=>$list,
                    "version"=>$version
                );
                if($request->try_get("callback")) {
                    return $this->jsonp($arr);
                }
                return $arr;
                break;
            default:
                break;

        }
    }
}