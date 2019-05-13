<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/2 0002
 * Time: 下午 3:12
 */

namespace load;


class app
{
    protected $call_back=[];
    public function call_back(){
        foreach ($this->call_back as $value){
            if($value["params"]==null){
                call_user_func([$value["object"],$value["method"]]);
            }
            else{
                call_user_func_array([$value["object"],$value["method"]],$value["params"]);
            }
        }
        $this->call_back=[];
    }
    public function add_call_back($object,$method,array $params=null){
        $this->call_back[]=["object"=>$object,"method"=>$method,"params"=>$params];
    }
    public function __destruct()
    {
        $this->call_back();
        // TODO: Implement __destruct() method.
    }
}