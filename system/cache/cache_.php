<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 10:38
 */

namespace system\cache;


use system\file;
use system\config\config;

class cache_
{
    protected $driver="file";
    protected $path="filesystem";
    protected $redis=null;
    protected $file=null;
    public function __construct()
    {
        //$this->init_config();//init cache config set the driver and path/key;
        if($this->driver=="redis") {
            $this->redis = new \Redis();
            $this->redis->connect("127.0.0.1", 6379);
        }
        if($this->driver=="file"){
            $this->file=new file();
            $this->path=dirname(dirname(dirname(__FILE__)))."/$this->path/";
        }
    }
    protected function init_config(){
        $this->driver=config::cache()['driver'];
        $this->path=config::cache()['path'];
    }//when cache init we will check driver and init_driver;
    public  function set_cache($key,$value,$expire,$is_serialize=false){
        if ($this->driver=="file")
        {
            if(!$is_serialize) {
                $this->file->write_file($this->path . md5($key) . ".txt", json_encode(["value" => $value, "expire" => time()+$expire,"key"=>$key]));//defalut
            }
            else{
                $this->file->write_file($this->path . md5($key) . ".txt", serialize(["value" => $value, "expire" => time()+$expire,"key"=>$key]));//if you want to store a object or mrthod use set_cache(($key,$value,$expire,$is_serialize=true)
            }
        }
        if($this->driver=="redis")
        {
            if(!$is_serialize) {
                $this->redis->hSet($this->path, md5($key), json_encode(["value" => $value, "expire" => time()+$expire,"key"=>$key]));
            }
            else{
                $this->redis->hSet($this->path, md5($key), serialize(["value" => $value, "expire" => time()+$expire,"key"=>$key]));
            }
        }
    }
    //defalut driver file
    //use faster driver redis
    //if you want to store a object or mrthod use set_cache(($key,$value,$expire,$is_serialize=true)
    public function get_cacahe($key,$is_serialize=false)
    {
        if($this->driver=="file")
        {
            $cache=$this->file->read_file($this->path . md5($key) . ".txt");
            if(($cache=$this->file->read_file($this->path . md5($key) . ".txt"))!=false){
                if($is_serialize==false) {
                    $cache = json_decode($cache, true);
                }else{
                    $cache=unserialize($cache);
                }
                if($this->is_expired($cache["expire"])){
                    return $cache["value"];
                }else{
                    $this->delete_key($key);
                    return null;
                }
            }else{
                return null;
            }
        }
        if($this->driver=="redis"){
            if($this->redis->hExists($this->path,md5($key))){
                $cache=$this->redis->hGet($this->path,md5($key));
                if(!$is_serialize) {
                    $cache=json_decode($cache,true);
                }else{
                    $cache=unserialize($cache);
                }
                if($this->is_expired($cache["expire"])){
                    return $cache["value"];
                }else{
                    return null;
                    $this->delete_key($key);
                }
            }
            else{
                return null;
            }
        }
    }//get cache if cache key is exist and the cache is_not_expired return cache value if cache has_been_expired detele it and return null;
    public function is_expired($expire){
        if($expire-time()>0){
            return true;
        }
        return false;
    }
    //check cache whether expreid
    public function delete_key($key){
        if($this->driver=="file"){
            $this->file->delete_file($this->path.md5($key).".txt");
        }
        if($this->driver=="redis"){
            $this->redis->hDel($this->path,md5($key));
        }
    }
    //delete cache by key
    public function get_all(){
        $cache_data_list=[];
        if($this->driver=='file'){
            foreach ($cache_list=$this->file->file_walk($this->path) as $value){
                $cache_data_list[]=$this->file->read_file($value);
            }
        }
        if($this->driver=="redis"){
            foreach ($this->redis->hGetAll($this->path) as $key=>$value){
                $cache_data_list[]=$value;
            }
        }
        return $cache_data_list;
    }
    //show all cache
    public function delete_all(){
        if($this->driver=='file'){
            foreach ($cache_list=$this->file->file_walk($this->path) as $value){
                $this->file->delete_file($value);
            }
        }
        if($this->driver=="redis"){
            $this->redis->del($this->path);
        }
    }
    //delete all cache
}