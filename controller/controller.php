<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/16 0016
 * Time: 下午 2:35
 */
namespace controller;
use load\provider;
use request\request;
use system\cache\cache;

class controller
{
    protected $cache_=null;
    protected $request_=null;
    public function __construct(request $request)
    {
        $this->request_=$request;
        $this->construct();
    }
    public function middlware($middlware){
        return provider::provider()->middleware($middlware,$this->request());
    }
    public function construct(){

    }
    public function response(){

    }
    public function download(){
        
    }//this is a method to ask client to download this data
    public function jsonp($callback_parms){
        $callback=$this->request()->get("callback");
        return sprintf("$callback(%s)", json_encode($callback_parms));
    }
    public function time()
    {
        return date("Y-m-d H:i:s");
    }
    public function is_1_array(array $arr){
        if (count($arr) == count($arr, 1)) {
            return true;
        } else {
            return false;
        }
    }
    public function cache(){
        if($this->cache_==null){
            $this->cache_=new cache();
            return $this->cache_;
        }
        return $this->cache_;
    }//magic method try to instance a cache object
    protected function request(){
        return $this->request_;
    }//magic method try to instance a request
}