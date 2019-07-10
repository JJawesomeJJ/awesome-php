<?php
/**
 * Created by awesome.
 * Date: 2019-06-24 05:36:05
 */
namespace http\middleware\csrf;
use http\middleware\middleware;
use request\request;
use system\common;
use system\cookie;
use system\Exception;

class csrf_middleware extends middleware
{
     public function check()
     {

     }
     public function sign_csrf_token(){
         if(!isset($_COOKIE["csrf_token"])) {
             $ssid=md5(microtime(true).common::rand(6));
             setcookie("csrf_token", $ssid, time() + 604800, "/", $_SERVER['HTTP_HOST'], false, true);
         }
         else{
             $ssid=cookie::get("csrf_token");
         }
         $token=md5(microtime(true).common::rand(6));
         $this->cache()->set_cache($ssid,$token,604800);
         return $token;
     }
     public function check_scrf_token(){
         if(!$this->request()->try_get("csrf_token")){
             new Exception("403","CSRF_ATTACT");
         }
         if(isset($_COOKIE["csrf_token"])) {
             if(!$this->cache()->get_cache($_COOKIE["csrf_token"])==$this->request()->get("csrf_token")){
                 new Exception("403","CSRF_ATTACT");
             }
         }
         else{
             new Exception("403","CSRF_ATTACT");
         }
     }
}