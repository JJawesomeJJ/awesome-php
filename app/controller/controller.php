<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/16 0016
 * Time: 下午 2:35
 */
namespace app\controller;
use load\provider_register;
use request\request;
use request\response;
use system\cache\cache;

//description our Business logic on here
class controller
{

    protected $cache_=null;
    protected $request_=null;
    public function __construct()
    {
        $this->request_=make(request::class);
        $this->construct();
    }
    public function middlware($middlware){
        return provider_register::provider()->middleware($middlware,$this->request());
    }
    public function construct(){

    }
    public function response(){

    }
    public function download($file_path,$file_name=false){
        return make(response::class)->download($file_path,$file_name);
    }//this is a method to ask client to download this data
    public function jsonp($callback_parms){
        $callback=$this->request()->get("callback");
        return make(response::class)->jsonp($callback,$callback_parms);
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