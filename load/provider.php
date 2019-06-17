<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/26 0026
 * Time: 上午 8:20
 */

namespace load;
use http;
use controller;
use request\request;
use system\Exception;

class provider
{
    protected $middleware=[];
    protected $controller=[];
    protected $dependencies=[];
    protected $container=[];
    public function controller($controller_name,$params=false){
//        if($params==false) {
//            return new $this->controller[$controller_name]();
//        }
//        else{
//            return new $this->controller[$controller_name]($params);
//        }
        return $this->controller[$controller_name];
    }
    public function middleware($middleware,$request){
        return new $this->middleware[$middleware]($request);
    }
    public function make($class_name)
    {
        if (array_key_exists($class_name, $this->container)) {
            return $this->container[$class_name];
        }
        $object=null;
        $class_name_fact=null;
//        try{
//            $object=new \ReflectionClass($this->dependencies[$class_name]);
//            $class_name_fact=$this->dependencies[$class_name];
//        }
        try {
            if (array_key_exists($class_name, $this->dependencies)) {
                try {
                    $object = new \ReflectionClass($this->dependencies[$class_name]);
                    $class_name_fact = $this->dependencies[$class_name];
                }
                catch (\Throwable $throwable){
                    $object = new \ReflectionClass("/".$this->dependencies[$class_name]);
                    $class_name_fact = $this->dependencies[$class_name];
                }
            } else {
                $object = new \ReflectionClass("\\$class_name");
                $class_name_fact = "\\$class_name";
            }
        }
        catch (\Throwable $throwable){
            $class_name_list=explode("\\",$class_name);
            $class_name_=$class_name_list[count($class_name_list)-1];
            $object=new \ReflectionClass("\\$class_name_");
        }
        catch (\Throwable $throwable){
        }
        $contruct_params_list=$this->get_class_contruct_params($object);
        if ($contruct_params_list==null||count($contruct_params_list) == 0) {
            $this->container[$class_name] = new $class_name_fact();
            return $this->container[$class_name];
        } else {
            foreach ($contruct_params_list as $value) {
                if($value==null){
                    continue;
                }
                if (!array_key_exists($value, $this->controller)) {
                    $this->make($value);
                }
            }
        }
        $params_list = [];
        foreach ($contruct_params_list as $value) {
            $params_list[] = $this->container[$value];
        }
        $class_object = $object->newInstanceArgs($params_list);
        $this->container[$class_name] = $class_object;
        return $class_object;
    }
    public function make_method($method,$class_name=false){
        if($class_name!=false) {
            $class_name_ = $class_name;
            $method_ = $method;
            $class_name = new \ReflectionClass($class_name);
            if ($class_name->hasMethod($method)) {
                $method = $class_name->getMethod($method);
                $method_params = [];
                foreach ($method->getParameters() as $parameter) {
                    $method_params[] = make($parameter->getClass()->getShortName());
                }
                return call_user_func_array([make($class_name_), $method_], $method_params);
            }
            new Exception("500", "method_can't be resolve");
        }
        else{
//            $method_=$method;
//            $method=new \ReflectionObject($method);
//            print_r($method->getConstructor()->getParameters());
        }
    }
    public function get_class_contruct_params(\ReflectionClass $object)
    {
        $params_list = [];
        $params = $object->getConstructor()->getParameters();
        if($params==null){
            return $params_list;
        }
        foreach ($params as $value) {
            try {
                $class_object=$value->getClass();
                if($class_object==null){
                    $params_list[]=null;
                    continue;
                }
                $params_list[] = $class_object->getShortName();
            } catch (\Throwable $throwable) {
                preg_match_all("/Class ([\s\S]*?) does/", $throwable->getMessage(), $matchs, PREG_SET_ORDER);
                $params_list[]=$matchs[0][1];
            }
        }
        return $params_list;
    }
    //debug when some class not define namespace php will throw a error try catch it!;
}