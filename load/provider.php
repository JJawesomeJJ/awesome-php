<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/26 0026
 * Time: 上午 8:20
 */

namespace load;
use app\providers\AppServiceProvider;
use http;
use request\request;
use system\config\config;
use system\Exception;

class provider
{
    protected $middleware=[];
    protected $controller=[];
    protected $dependencies=[];
    protected $container=[];
    protected $class_factory=[];
    protected $console=[];
    public function controller($controller_name,$params=false){
        if(class_exists($controller_name)){
            return $controller_name;
        }
        if(!is_cli()) {
            if (!array_key_exists($controller_name, $this->controller)) {
                throw new \Exception($controller_name . ' NOT FIND');
            }
            return $this->controller[$controller_name];
        }else{
            if (!array_key_exists($controller_name, $this->console)) {
                throw new \Exception($controller_name . ' NOT FIND');
            }
            return $this->console[$controller_name];
        }
    }
    public function get_dependencies(){
        return $this->dependencies;
    }
    protected function __construct()
    {
    }
    public function ServiceProvider(){
        foreach (config::provider() as $item){
            $this->make_method("register",$item);
        }
    }
    public function middleware($middleware){
        if (class_exists($middleware)){
            return make($middleware);
        }
        return make($this->middleware[$middleware]);
    }
    public function make($class_name,$args=[])
    {
        if(array_key_exists($class_name,$this->container)){
            return $this->container[$class_name];
        }
        $needs_class=[];
        if(!class_exists($class_name)){
            if(array_key_exists($class_name,$this->dependencies)){
                $class_name=$this->dependencies[$class_name];
            }else{
//                throw new \Exception($class_name);
                throw new \Exception(404,"Class $class_name Not Find");
            }
        }
        if (array_key_exists($class_name, $this->container)) {
            return $this->container[$class_name];
        }
        if(array_key_exists($class_name,$this->class_factory)){
            $info=$this->class_factory[$class_name];
            if($info['closure'] instanceof \Closure) {
                $object = $this->make_closure($info['closure']);
                if ($info['is_singleton']) {
                    $this->container[$class_name] = $object;
                }
                return $object;
            }
            else{
                $class_object=new \ReflectionClass($class_name);
                $params_list=$this->get_class_contruct_params($class_object);
                $this_needs_params=[];
                foreach ($params_list as $item){
                    $this_needs_params[]=$this->make($item);
                }
                $class_object->newInstanceArgs($this_needs_params);
                if($info['is_singleton']){
                    $this->controller[$class_name]=$class_object;
                }
                return $class_object;
            }
        }
        $object=new \ReflectionClass($class_name);
        $contruct_params_list=$this->get_class_contruct_params($object);
        if ($contruct_params_list==null||count($contruct_params_list) == 0) {

        } else {
            foreach ($contruct_params_list as $value) {
                if($value==null){
                    continue;
                }
                $needs_class[]=$this->make($value);
            }
        }
        return $object->newInstanceArgs($needs_class);
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
            new Exception("500", "$method method_can't be resolve");
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
        $params = $object->getConstructor();
        if($params==null){
            return [];
        }
        if(($params=$params->getParameters())==null){
            return [];
        }
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
    public function make_mothod_static($class_name,$method,$params){
        if(!class_exists($class_name)){
            if(array_key_exists($class_name,$this->dependencies)){
                $class_name=$this->dependencies[$class_name];
            }
            else{
                new Exception('404',"class $class_name not find");
            }
        }
        $params=[$params];
        return call_user_func_array([$class_name,$method],$params);
    }
    public function add_dependencies(array $dependencies){
        $this->dependencies=array_merge($this->dependencies,$dependencies);
    }
    //bind factory class create
    public function bind($class_name,$closure,$is_singleton=false){
        if(!class_exists($class_name)){
            if(array_key_exists($class_name,$this->dependencies)){
                $class_name=$this->dependencies[$class_name];
            }else{
                new Exception(404,"CLASS $class_name Not Find!");
            }
            $this->class_factory[$class_name]=['closure'=>$closure,'is_singleton'=>$is_singleton];
        }else{
            $this->class_factory[$class_name]=['closure'=>$closure,'is_singleton'=>$is_singleton];
        }
    }
    //singleton
    public function singleton($class_name,$closure){
        $this->bind($class_name,$closure,true);
    }
    public function get_class_path($class_name){
        if(class_exists($class_name)){
            return $class_name;
        }
        if(array_key_exists($class_name,$this->dependencies)){
            return $this->dependencies[$class_name];
        }else {
            new Exception(404, 'event path not find');
        }
    }
    //debug when some class not define namespace php will throw a error try catch it!;
    public function call_back(){

    }
    public function setAttribute($class_name,$object){
        if(!class_exists($class_name)) {
            if (!array_key_exists($class_name, $this->container)) {
                throw new \Exception("Class " . $class_name . " Not find");
            }
        }
        $class_name=get_class($class_name);
        $this->container[$class_name]=$object;
    }

    /**
     *
     * @param \Closure $closure
     * @return mixed
     * @throws \ReflectionException
     */
    public function make_closure(\Closure $closure){
        $object=new \ReflectionFunction($closure);
        $params=[];
        foreach ($object->getParameters() as $parameter){
            $params[]=$this->make($parameter->getClass()->getName());
        }
        return call_user_func_array($closure,$params);
    }
}