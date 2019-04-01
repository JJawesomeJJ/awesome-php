<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25 0025
 * Time: 下午 6:57
 */

namespace http\middleware\sql_middleware;


use http\middleware\middleware;

class sql_middleware extends middleware
{

    public function check()
    {
        // TODO: Implement check() method.
    }
    public function sql_inject_check(){
        $arr=$this->user_input;
        foreach ($arr as $key=>$value)
        {

        }
    }
}