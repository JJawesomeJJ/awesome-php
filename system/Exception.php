<?php
/*
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/19 0019
 * Time: 下午 10:00
 */

namespace system;


use system\config\config;

class Exception
{
    public function __construct($code,$message)
    {
        if($this->is_cli()){
            $this->throw_exception($message);
            return;
        }
        if(config::debug()['status']) {
            try {
                throw new \Exception($message);
            }
            catch (\Throwable $throwable){
                echo json_encode(["code" => $code, "message" => $message,'details'=>$throwable->getTraceAsString()]);
                die();
            }
        }else{
            echo json_encode(["code" => $code, "message" => $message]);
        }
        exit();
    }
    protected function throw_exception($message){
        throw new \Exception($message);
    }
    protected function is_cli()
    {
        return preg_match("/cli/i", php_sapi_name()) ? true : false;
    }
}