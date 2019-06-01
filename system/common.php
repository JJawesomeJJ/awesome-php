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
}