<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 3:59
 */
namespace routes;
use controller\auth\auth_controller;
use controller\controller;
use http\middleware\middleware;
use load\provider_register;
use PhpParser\Node\Expr\Closure;
use request\request;
use SebastianBergmann\CodeCoverage\Report\PHP;
use system;

class routes
{
    protected static $route = [
        "GET"=>[],
        "POST"=>[],
        "DELETE"=>[],
        "ANY"=>[],
        "PUT"=>[],
    ];
    protected static $object;
    protected static $now_request;
    private $request = null;
    protected $now_request_url_list;
    protected $value;//已匹配的路由信息
    public function __construct()
    {
        $this->request =make("request");
        self::$object=$this;
    }
    protected function start()
    {
        if ($this->pathinfo()) {
            $values=$this->value;
            $provider = provider_register::provider();
            if(count($values["middleware"])>0) {
                if (gettype($values["middleware"][0]) == 'string') {
                    $values["middleware"] = [$values["middleware"]];
                }
            }
            foreach ($values["middleware"] as $middleware) {
                if(gettype($middleware)=="array"){
                    switch (count($middleware)){
                        case 2:
                            $object = $provider->middleware($middleware[0],$this->request);
                            call_user_func([$object,$middleware[1]]);
                            $this->request = $object->next();
                            //无参数调用中间件的函数
                            break;
                        case 3:
                            $object=$provider->middleware($middleware[0],$this->request);
                            if(gettype($middleware[2])=='string')
                            {
                                $middleware[2]=["$middleware[2]"];
                            }
                            //有参数的调用中间的函数
                            call_user_func_array([$object,$middleware[1]],$middleware[2]);
                            $this->request = $object->next();
                            //edit request obejct which has been handle with middleware
                            break;
                        default:
                            break;
                    }
                }
                else{
                    $object = $provider->middleware($middleware[0],$this->request);
                    $this->request=$object->next();
                    //加载中间件中间件在check完成自我调用
                }
            }
            $response = null;
            if (gettype($values["controller_method"]) == "object") {
                $response = call_user_func($values["controller_method"]);//if is a object which is a anonymous return value so it is echo value
            } else {
                $controller_method = explode("@", $values["controller_method"]);
                //$response = $this->load_method($provider->controller($controller_method[0], $this->request), $controller_method[1]);
                $response = $provider->make_method($controller_method[1],$provider->controller($controller_method[0]));
            }
            if (gettype($response) == 'array') {
                echo json_encode($response);
                return;
            } else {
                echo $response;
                return;
            }
        }
        echo json_encode(['code'=>'404','message'=>'page_not_exist']);
    }
    public function __call($name, $arguments)
    {
        if($name=="start"){
            $this->start();
        }
    }

    // when no url match routes then app echo 404 page
    protected function pathinfo(){
        foreach (array_merge(self::$route[$this->request->request_mothod()],self::$route["ANY"]) as $route){
            if (strrpos($route["url"], "{") !== false) {
                if($this->vertify_pathinfo($route)){
                    return true;
                }
                else{
                    continue;
                }
            }
            else{
                if('/'.$route["url"]==$this->request->get_url()){
                    $this->value=$route;
                    return true;
                }
            }
        }
        return false;
    }
    protected function vertify_pathinfo($route){
        $route_rule_list = explode('/', '/'.$route["url"]);
        $route_params = [];
        $now_request_url = $this->get_now_request_url();
        if(count($now_request_url)!=count($route_rule_list))
        {
            return false;
        }
        for ($i = 0; $i < count($route_rule_list); $i++) {
            if (strrpos($route_rule_list[$i], "{") !== false) {
                $key=str_replace('}', "", str_replace('{', "", $route_rule_list[$i]));
                if($now_request_url[$i]==""){
                    new system\Exception("400","request_key_not_exist_expect_$key");
                }
                $route_params[$key] = $now_request_url[$i];
                continue;
            } else {
                if ($route_rule_list[$i] != $now_request_url[$i]) {
                    return false;
                }
            }
        }
        $this->request->user_input = array_merge($this->request->user_input, $route_params);
        $this->value=$route;
        return true;
    }
    protected function get_now_request_url(){
        if($this->now_request_url_list==null){
            $this->now_request_url_list=explode('/',$this->request->get_url());
        }
        return $this->now_request_url_list;
    }
    function load_method($object, $fun)
    {
        if(method_exists($object,$fun)){
            return $object->$fun();
        }
        else{
            new system\Exception("500","controller_method_error");
        }
    }
    public static function get($url,$controller_method,array $middleware=[]){
        self::$route["GET"][]=["request_method"=>"GET","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
        self::$now_request="GET";
        return self::$object;
    }
    public static function post($url,$controller_method,array $middleware=[]){
        self::$route["POST"][]=["request_method"=>"POST","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
        self::$now_request="POST";
        return self::$object;
    }
    public static function delete($url,$controller_method,array $middleware=[]){
        self::$route["DELETE"][]=["request_method"=>"DELETE","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
        self::$now_request="DELETE";
        return self::$object;
    }
    public static function put($url,$controller_method,array $middleware=[]){
        self::$route["PUT"][]=["request_method"=>"PUT","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
        self::$now_request="PUT";
        return self::$object;
    }
    public static function any($url,$controller_method,array $middleware=[]){
        self::$route["ANY"][]=["request_method"=>"ANY","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
        self::$now_request="ANY";
        return self::$object;
    }
    public function middleware($middleware_name,$method=false,$params=false){
        $index=count(self::$route[self::$now_request])-1;
        if($method&&$params){
            self::$route[self::$now_request][$index]["middleware"][]=[$middleware_name,$method,[$params]];
        }
        if($method&&!$params){
            self::$route[self::$now_request][$index]["middleware"][]=[$middleware_name,$method];
        }
        if(!$method&&!$params){
            self::$route[self::$now_request][$index]["middleware"][]=[$middleware_name];
        }
        return $this;
    }
    public function name($name){
        $index=count(self::$route[self::$now_request])-1;
        self::$route[self::$now_request][$index]['name']=$name;
        return $this;
    }
}
