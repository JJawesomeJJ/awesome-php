<?php
/*
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/19 0019
 * Time: 下午 10:00
 */

namespace system;


class Exception
{
    private $code;
    public function __construct($code,$message)
    {
        $this->code=$code;
        try {
            $this->throw_exception($message);
        }
        catch (\Exception $exception)
        {
            $this->exception_json($exception);
        }
    }
    public function throw_exception($message){
        throw new \Exception($message);
    }
    public function exception_json($message)
    {
        preg_match_all("/Exception: ([\s\S]*?) in/",$message, $matchs, PREG_SET_ORDER);
        echo json_encode(["code"=>$this->code,"message"=>$matchs[0][1]]);
        exit();
    }
}