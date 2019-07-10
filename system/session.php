<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/3 0003
 * Time: 上午 10:11
 */

namespace system;


use system\config\config;

class session
{
    protected static $session_object;
    public function __construct()
    {
        if(!isset($_SESSION)) {
            session_name(config::session()["name"]);
            session_start();
        }
    }
    protected static function init(){
        if(self::$session_object==null){
            self::$session_object=new session();
        }
    }
    public static function set($name,$value){
        self::init();
        $_SESSION[$name]=$value;
    }
    public static function get($name){
        self::init();
        if(!isset($_SESSION[$name])){
            return false;
        }
        return $_SESSION[$name];
    }
    public static function exist($name){
        self::init();
        if(!isset($_SESSION[$name])){
            return false;
        }
        return true;
    }
    public static function forget($name){
        self::init();
        unset($_SESSION[$name]);
    }
}