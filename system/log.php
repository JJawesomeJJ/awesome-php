<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/11 0011
 * Time: 上午 8:44
 */
namespace system;
use system\config\config;

class log
{
    protected $error_path;
    public function __construct()
    {
        $this->error_path=config::env_path().config::debug()["log_path"].'/';
    }
    public function write_log($error_message){
        if(!is_dir($this->error_path)){
            mkdir($this->error_path);
        }
        $fd = fopen($this->error_path.date("Y-m-d").".log", "a");
        fwrite($fd, $error_message . date('Y-m-d H:i:s', time()) . "\n");
        if(config::debug()['is_notify_admin']){
            $mail=new mail();
            $mail->send_email(config::user()['email'],$error_message,'error_notify'.config::index_path());
        }
    }
}