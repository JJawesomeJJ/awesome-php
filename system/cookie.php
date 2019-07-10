<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/25 0025
 * Time: 下午 2:46
 */

namespace system;


class cookie
{
    public static function set($key,$value,$expired,$http_only=true,$path="/",$is_encrypt=true,$is_secure=false){
        if($is_encrypt){
            $value=encrypt::aes_encrypt($value);
        }
        setcookie($key,$value,time()+$expired,$path,$_SERVER['HTTP_HOST'],$is_secure,$http_only);
    }
    public static function get($key,$is_decrypt=true){
        if(isset($_COOKIE[$key])){
            if($is_decrypt) {
                return encrypt::aes_decrypt($_COOKIE[$key]);
            }
            return $_COOKIE[$key];
        }
        return false;
    }
    public static function forget($key){
        if(isset($_COOKIE[$key])){
            setcookie($key,null,time()-10,"/",$_SERVER['HTTP_HOST']);
        }
    }
}