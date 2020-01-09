<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/23 0023
 * Time: 下午 8:58
 */

namespace system\config;


class channel_config
{
    public static function config(){
        return [
            'driver'=>'rabbitmq',
            'port'=>6003
        ];
    }
    public static function rabbitmq(){
        return [
            'exchange'=>'awesome_channel',
            'queue'=>'awesome_queue'
        ];
    }
    //awesome_echo config
    public static function awesome_echo(){
        return [
            'online'=>'awesome_echo_online',//记录当前在线的用户-record online user
            'channel'=>'awesome_echo_channel',//记录当前所有的频道-record current all channel
            'channel_suffix'=>'awesome_echo',//频道的前缀
            'user_rel_id'=>'awesome_echo_online_real'//记录当前已经验证的用户的id
        ];
    }
}