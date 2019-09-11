<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/3 0003
 * Time: 上午 8:43
 */

namespace system;


class redis
{
    protected $redis;//连接对象
    public function __construct()
    {
        $this->redis=class_define::redis();
    }
    public function get($key){
        return $this->redis->get($key);
    }
    public function set($key,$value,$expired=false){
        if(is_string($value)){
            $value=json_encode($value);
        }
        if($expired!=false){
            if(is_numeric($expired)){
                new Exception('400','expired_should_be_number');
            }
            $this->redis->set($key,$value,$expired);
        }
        else{
            $this->redis->set($key,$value);
        }
    }
    public function delete($key){
        $this->redis->del($key);
    }
    public function increment($key,$value=1){
        $this->redis->incrBy($key,$value);
    }
    public function decrement($key,$value=1){
        $this->redis->decrBy($key,$value);
    }
}