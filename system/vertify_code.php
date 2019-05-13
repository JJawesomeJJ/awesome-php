<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/18 0018
 * Time: 下午 4:59
 */

namespace system;


class vertify_code
{
    public static function random_code($number){
        $code='';
        $data = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for($i=0;$i<$number;$i++)
        {
            $code.=$data[rand(0,strlen($data)-1)];
        }
        return $code;
    }
}