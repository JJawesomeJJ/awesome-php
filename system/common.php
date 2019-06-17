<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26 0026
 * Time: 下午 2:10
 */

namespace system;


class common
{
    public static function is_timestamp($timestamp) {
        if(strtotime(date('m-d-Y H:i:s',$timestamp)) === $timestamp) {
            return true;
        } else {
            return false;
        }
    }
    public static function get_array_value(array $keys,array $arr,$without_of_key=false){
        $return_arr=[];
        foreach ($keys as $key){
            if(!array_key_exists($key,$arr))
            {
                new Exception("400","array_key_not_exist_$key");
            }
            $return_arr[$key]=$arr[$key];
        }
        if($without_of_key){
            return array_values($return_arr);
        }
        return $return_arr;
    }
}