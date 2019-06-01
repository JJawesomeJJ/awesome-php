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
use request\request;
use system;

class routes
{
    protected static $route = [
    ];
    protected $routes = [];
    private $request = null;
    public function __construct()
    {
        $this->routes=self::$route;
        $this->request =make("request");
        $this->request_check();
    }
    private function request_check()
    {
        foreach ($this->routes as $values) {
            if ($values["request_method"] == $this->request->request_mothod() && $values["url"] == $this->request->get_url()) {
                $provider = new provider_register();
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
                    $response = $this->load_method($provider->controller($controller_method[0], $this->request), $controller_method[1]);
                }
                if (gettype($response) == 'array') {
                    echo json_encode($response);
                    return;
                } else {
                    echo $response;
                    return;
                }
            }
        }
        echo json_encode(['code'=>'404','message'=>'page_not_exist']);
    }
        // when no url match routes then app echo 404 page
    function LoadMethod($object, $fun)
    {
        $object=new \ReflectionClass($object);
        if ($object->hasMethod($fun)) {
            $tmp=$object->getMethod($fun);
            if ($tmp->ispublic()) {
                return $tmp->invoke($object->newInstance());
            } else {
                throw new \Exception("call_fun_error");
            }
        } else {
            throw new \Exception("is_not_exist_fun");
        }
    }//
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
        self::$route[]=["request_method"=>"GET","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
    }
    public static function post($url,$controller_method,array $middleware=[]){
        self::$route[]=["request_method"=>"POST","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
    }
    public static function delete($url,$controller_method,array $middleware=[]){
        self::$route[]=["request_method"=>"DELETE","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
    }
    public static function put($url,$controller_method,array $middleware=[]){
        self::$route[]=["request_method"=>"PUT","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
    }
    public static function any($url,$controller_method,array $middleware=[]){
        self::$route[]=["request_method"=>"ANY","url"=>$url,"controller_method"=>$controller_method,"middleware"=>$middleware];
    }
}
