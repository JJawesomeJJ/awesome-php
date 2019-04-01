<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/18 0018
 * Time: 下午 4:53
 */

namespace system;


class template
{
    public function template($name,array $arr){
        $data=file::read_file("/var/www/html/php/template/$name.html");
        foreach($arr as $key=>$value)
        {
            $data=str_replace("{{{$key}}}",$value,$data);
        }
        return $data;
    }
}