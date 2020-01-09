<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/13 0013
 * Time: 下午 9:24
 */

namespace system;


class lock
{
    protected static $lock_key_list=[];
    public static function redis_lock($key,$lock_time,$max_delay_time){
        $time=0;
        $lock_key="redis_lock_key".md5($key);
        $redis=class_define::redis();
        while (!$redis->setnx($lock_key,time())){
            $time=$time+100000/1000000;
            if($time>=$max_delay_time){
                new Exception(403,'get lock timeout!');
            }
            usleep(100000);//休息0.1s
        }
        self::$lock_key_list[]=$key;
        //将获取的键加入的数组中
        $redis->expire($lock_key,$lock_time);
        return true;
    }
    public static function redis_unlock($key){
        $lock_key="redis_lock_key".md5($key);
        if(in_array($key,self::$lock_key_list)){
            class_define::redis()->del($lock_key);
        }else{
            new Exception(404,'DO NOT HAVE PERMISSION TO DELETE IT!!');
        }
    }
    public function flush_all(){
        $lock_key="redis_lock_key";
        foreach (self::$lock_key_list as $key){
            class_define::redis()->del($lock_key.md5($key));
        }
    }
}