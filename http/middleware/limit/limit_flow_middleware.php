<?php
/**
 * Created by awesome.
 * Date: 2019-04-21 10:33:44
 */
namespace http\middleware\limit;
use http\middleware\middleware;
use system\cache\cache_;
use system\Exception;
//description it is a middleware which can limit ip request frequency and limit request app to max_reuqest_number
class limit_flow_middleware extends middleware
{
     public function check()
     {
        // TODO: middleware 入口.
         //$this->ip_limit(20);
         //$this->leaky_bucket("mode",50);
//         if($this->params!=null) {
//             $method_name = $this->params["method"];
//             $this->$method_name($this->params["time"]);
//         }
     }
     public function ip_limit($max_request){
         $cache_data=$this->cache()->lock_get_cache($this->request()->get_ip_address());
         if($cache_data==null){
             $this->cache()->lock_set_cache($this->request()->get_ip_address(),["time"=>date("Y-m-d H:i"),"num"=>1,"now_request"=>1],86400);
         }
         else{
             if($cache_data['now_request']>$max_request){
                 new Exception('403','ip_has_been_limit');
             }
             $cache_data['now_request']=$cache_data['now_request']+1;
             if($cache_data["time"]==date("Y-m-d H:i")){
                 if($cache_data["num"]>5){
                     new Exception("403","request_frequetly");
                 }
                 else{
                     $cache_data["num"]=$cache_data["num"]+1;
                     $this->cache()->lock_set_cache($this->request()->get_ip_address(),$cache_data,86400);
                 }
             }
             else{
                 $cache_data["time"]=date("Y-m-d H:i");
                 $cache_data['num']=0;
                 $this->cache()->lock_set_cache($this->request()->get_ip_address(),$cache_data,86400);
             }
         }
     }//
     public function leaky_bucket($max_capacity){
         $leaky_bucket_cache=$this->cache()->lock_get_cache($this->request()->get_url());
         if($leaky_bucket_cache==null){
             $this->cache()->set_cache($this->request()->get_url(),["now_capacity"=>1],"forever");
             return;
         }
         else{
             if($leaky_bucket_cache["now_capacity"]>=$max_capacity){
                 new Exception("403","more_than_api_capacity");
             }
             else{
                 $leaky_bucket_cache["now_capacity"]=$leaky_bucket_cache["now_capacity"]+1;
                 $this->cache()->lock_set_cache($this->request()->get_url(),$leaky_bucket_cache,"forever");
                 app()->add_call_back($this,"leavy_leaky_bucket");
             }
         }
     }//when user try to request app check the max_capacity
     public function leavy_leaky_bucket(){
         $leaky_bucket_cache=$this->cache()->lock_get_cache($this->request()->get_url());
         $leaky_bucket_cache["now_capacity"]=$leaky_bucket_cache["now_capacity"]-1;
         $this->cache()->lock_set_cache($this->request()->get_url(),$leaky_bucket_cache,"forever");
     }//when user leavy app refresh now_capacity
}