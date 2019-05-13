<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/16 0016
 * Time: 下午 2:35
 */
namespace controller;
use request\request;
use system\cache\cache;
use system\cache\cache_;

class controller
{
    protected $cache_=null;
    protected $request_=null;
    public function __construct($request=false)
    {
        if($request!=false){
            $this->request_=$request;
        }
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
    public function cache(){
        if($this->cache_==null){
            $this->cache_=new cache();
            return $this->cache_;
        }
        return $this->cache_;
    }//magic method try to instance a cache object
    protected function request(){
        if($this->request_==null){
            $this->request_=new request();
            return $this->request_;
        }
        else{
            return $this->request_;
        }
    }//magic method try to instance a request
}