<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/16 0016
 * Time: 下午 2:35
 */
namespace controller;
use system\cache\cache;

class controller
{
    protected $cache_=null;
    public function __construct()
    {

    }
    public function response(){

    }
    public function download(){
        
    }
    public function jsonp($callback,$callback_parms){
        return sprintf("$callback(%s)", json_encode($callback_parms));
    }
    public function time()
    {
        return date("Y-m-d H:i:s");
    }
    public function cache(){
        if($this->cache_==null){
            return $this->cache_=new cache();
        }
        return $this->cache_;
    }
}