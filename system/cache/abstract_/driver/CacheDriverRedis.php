<?php
/**
 * @athor jjawesome
 * @description it is a driver of cache use 'redis' and 'luascript'
 */
namespace system\cache\abstract_\driver;
use system\cache\abstract_\CacheDriver;
use system\class_define;
use system\lock;

class CacheDriverRedis extends CacheDriver
{
    protected $con;
    protected $hash_path="redis-awesome-cache";
    public function __construct()
    {
        $this->con=class_define::redis();
    }
    public function set(string $key, $value, $expire = "forever")
    {
        $this->con->hSet($this->hash_path,md5($key),json_encode(["value" => $value,"created_at" => time(),"expire"=>$expire,"key"=>$key]));
    }

    public function get(string $key)
    {
        $cache_info=$this->con->hGet($this->hash_path,md5($key));
        if(is_null($cache_info)){
            return null;
        }
        $cache_info=json_decode($cache_info,true);
        if($this->is_expire($cache_info['created_at'],$cache_info['expire'])){
            $this->del($key);
            return null;
        }
        return $cache_info['value'];
    }
    public function getorset(string $key, \Closure $fun, $expire="forever")
    {
        $result=$this->get($key);
        if(!is_null($result)){
            return null;
        }
        $result=call_user_func([$fun]);
        $this->set($key,$result,$expire);
        return $result;
    }
    public function lock(string $key, int $timeout, int $max_fail_timeout)
    {
        $key=md5($this->hash_path.$key);
        lock::redis_lock($key,$timeout,$max_fail_timeout);
    }
    public function unlock(string $key)
    {
        $key=md5($this->hash_path.$key);
        lock::redis_unlock($key);
    }
    public function incrBy(string $key,$expire="forever",int $default = 1,$incr_value=1)
    {
        $script=<<<EOT
        local result=redis.call('hGet',KEYS[1],KEYS[2]);
        if result==nil or result==false then
           result={}
           result['key']=ARGV[5]
           result['value']=ARGV[1]
           result['created_at']=ARGV[4]
           result['expire']=ARGV[3]
           redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result))
           return ARGV[1]
        end
        result=cjson.decode(result)
        local value1=result['value']
        if tonumber(value1) then
            if result['expire']~="forever" then
               if (tonumber(result['expire'])+tonumber(result['created_at']))<=tonumber(ARGV[4]) then
                  result={}
                  result['key']=KEYS[2]
                  result['value']=ARGV[1]
                  result['created_at']=ARGV[4]
                  result['expire']=ARGV[3]
                  redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result))
                  return ARGV[2]
               end
            end
            result['value']=result['value']+ARGV[2]
            redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result))
            return result['value']
        else
            return "format err the value ==>"..result['value']
        end
EOT;
        $result=$this->con->eval($script,[$this->hash_path,md5($key),$default,$incr_value,$expire,time(),$key],2);
//        print_r($result);die();
        if(!is_numeric($result)){
            throw new \Exception("unsoporrt format ".$this->con->hGet($this->hash_path,md5($key)));
        }
        return $result;
    }
    public function get_all(): array
    {
        $result=[];
        foreach ($this->con->hGetAll($this->hash_path) as $key=>$value){
            $cahce_info=json_decode($value,true);
            if($this->is_expire($cahce_info['created_at'],$cahce_info['expire'])){
                $this->del($key);
            }else{
                $result[$key]=$cahce_info['value'];
            }
        }
        return $result;
    }
    public function del_all()
    {
        $this->con->del($this->hash_path);
    }
    public function decrBy(string $key,$expire="forever",int $default = 1,$decr_value=1)
    {
        $script=<<<EOT
        local result=redis.call('hGet',KEYS[1],KEYS[2]);
        if result==nil or result==false then
           result={}
           result['key']=ARGV[5]
           result['value']=ARGV[1]
           result['created_at']=ARGV[4]
           result['expire']=ARGV[3]
           redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result))
           return ARGV[1]
        end
        result=cjson.decode(result)
        local value1=result['value']
        if tonumber(value1) then
            if result['expire']~="forever" then
               if (tonumber(result['expire'])+tonumber(result['created_at']))<=tonumber(ARGV[4]) then
                  result={}
                  result['key']=KEYS[2]
                  result['value']=ARGV[1]
                  result['created_at']=ARGV[4]
                  result['expire']=ARGV[3]
                  redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result))
                  return ARGV[2]
               end
            end
            result['value']=result['value']-ARGV[2]
            redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result))
            return 1
        else
            return "format err the value ==>"..result['value']
        end
EOT;
        $result=$this->con->eval($script,[$this->hash_path,md5($key),$default,$decr_value,$expire,time(),$key],2);
//        print_r($result);die();
        if(!is_numeric($result)){
            throw new \Exception("unsoporrt format ".$this->con->hGet($this->hash_path,md5($key)));
        }
        return $result;
    }
    public function del(string $key)
    {
        return $this->con->hDel($this->hash_path,md5($key));
    }
    public function exist(string $key): bool
    {
        if(is_null($this->get($key))){
            return false;
        }
        return true;
    }
}