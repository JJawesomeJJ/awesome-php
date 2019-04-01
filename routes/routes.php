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
use PHPMailer\PHPMailer\Exception;
use load\provider_register;

class routes
{
    protected $routes=[
        [
        ]
    ];
    private $request_url;
    private $request_method;
    private function request(){
        $url=$_SERVER['PHP_SELF'];
        $index=strrpos($url,".php/");
        $this->request_url=substr($url,$index+5,strlen($url)-$index+1);
        $this->request_method=strtolower($_SERVER['REQUEST_METHOD']);
    }
    public function __construct()
    {
        $this->request();
        $this->request_check();

    }
    private function request_check(){
        foreach ($this->routes as $values)
        {
            if($values[0]==$this->request_method&&$values[1]==$this->request_url)
            {
                $provider=new provider_register();
                if(isset($values[4]))
                {
                    $object=$provider->middleware($values[4]);
                }
                $response=$this->load_method($provider->controller($values[2]),$values[3]);
                if(gettype($response)=='array')
                {
                    echo json_encode($response);
                    return;
                }
                else{
                    echo $response;
                    return;
                }
            }
        }
        echo json_encode(['code'=>'404','message'=>'page_not_exist']);
    }
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
    }
    function load_method($object, $fun)
    {
        if(method_exists($object,$fun)){
            return $object->$fun();
            } else {
                throw new \Exception("call_fun_error");
            }
    }
    public function load_controller(){
        foreach ($this->routes as $values)
        {
            if($values[0]==$this->request_method&&$values[1]==$this->request_url)
            {
                if($values[4]!=null)
                {
                    $object=provider_register::middleware($values[4]);
                }
                $response=$this->LoadMethod($values[2],$values[3]);
                if(gettype($response)=='array')
                {
                    echo json_encode($response);
                    return;
                }
                else{
                    echo $response;
                    return;
                }
            }
        }
        echo json_encode(['code'=>'404','message'=>'page_not_exist']);
    }
}