<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 10:38
 */

namespace system\cache;


use function Couchbase\fastlzDecompress;
use system\Exception;
use system\file;
use system\config\config;

class cache_
{
    protected $driver="file";//cache driver
    protected $timeout=1; //when you want to lock a cahce object which has been lock process will spleep and try again and set timeout
    protected $path="filesystem";//cache store path when cahce driver is redis this is hash key
    protected $redis=null; //redis object
    protected $file=null; //file object
    private $redis_lock=[];//when you lock a cache object but forget unlock it when end of process cache try to help unlock it and avoid deadlock when driver is file when file object destory process will unlock it
    public function __construct()
    {
        $this->init_config();//init cache config set the driver and path/key;
        if($this->driver=="redis") {
            $this->redis = new \Redis();
            $this->redis->connect("127.0.0.1", 6379);
        }
        if($this->driver=="file"){
            $this->file=new file();
            $this->path=dirname(dirname(dirname(__FILE__)))."/$this->path/";//set file store path
        }
    }
    protected function init_config(){
        $this->driver=config::cache()['driver'];
        $this->path=config::cache()['path'];
    }//when cache init we will check driver and init_driver;
    public  function set_cache($key,$value,$expire,$is_serialize=false){
        if(is_numeric($expire)){
            $expire=$expire+time();
        }
        if ($this->driver=="file")
        {
            if(!$is_serialize) {
                $this->file->write_file($this->path . md5($key) . ".txt", json_encode(["value" => $value, "expire" =>$expire,"key"=>$key]));//defalut
            }
            else{
                $this->file->write_file($this->path . md5($key) . ".txt", serialize(["value" => $value, "expire" => $expire,"key"=>$key]));//if you want to store a object or mrthod use set_cache(($key,$value,$expire,$is_serialize=true)
            }
        }
        if($this->driver=="redis")
        {
            if(!$is_serialize) {
                $this->redis->hSet($this->path, md5($key), json_encode(["value" => $value, "expire" => $expire,"key"=>$key]));
            }
            else{
                $this->redis->hSet($this->path, md5($key), serialize(["value" => $value, "expire" => $expire,"key"=>$key]));
            }
        }
    }
    //defalut driver file
    //use faster driver redis
    //if you want to store a object or mrthod use set_cache(($key,$value,$expire,$is_serialize=true)
    //if you want to store a cache forever please set_expire=='forever'
    public function get_cache($key,$is_serialize=false)
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
                    $this->delete_key($key);
                    return null;
                }
            }
            else{
                return null;
            }
        }
    }//get cache if cache key is exist and the cache is_not_expired return cache value if cache has_been_expired detele it and return null;
    //use it get_cache($key) if cache content is a object use it as get_cache($key,ture)
    public function is_expired($expire){
        if($expire=="forever"){
            return true;
        }//when set expire time forever the cahce will alaway store in you server
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
    public function lock_get_cache($key,$is_serialize=false)
    {
        if ($this->driver == "file") {
            if (($cache = $this->file->safy_read_file($this->path . md5($key) . ".txt")) != false) {
                if ($is_serialize == false) {
                    $cache = json_decode($cache, true);
                } else {
                    $cache = unserialize($cache);
                }
                if ($this->is_expired($cache["expire"])) {
                    return $cache["value"];
                } else {
                    $this->delete_key($key);
                    return null;
                }
            } else {
                return null;
            }
        }//try to lock the file fd
        if($this->driver=="redis"){
            if($this->redis->hExists($this->path,md5($key))){
                if(count($this->redis_lock)!==0){
                    foreach ($this->redis_lock as $value){
                        $this->unlock_cache($value);
                    }
                    //when second use it but last cache obejct without of unlock so unlock it
                }
                $this->try_lock_cache($key,time());//try to lock cache object
                $cache=$this->redis->hGet($this->path,md5($key));
                if(!$is_serialize) {
                    $cache=json_decode($cache,true);
                }else{
                    $cache=unserialize($cache);
                }
                if($this->is_expired($cache["expire"])){
                    return $cache["value"];
                }else{
                    $this->delete_key($key);
                    return null;
                }
            }
            else{
                return null;
            }
        }
    }
    private function try_lock_cache($key,$time){
        if(time()-$time>$this->timeout){
            new Exception("408","get_cache_lock_timeout");
        }//when process run time more than timeout so stop it
        $value=microtime(true)+1;
        $status=$this->redis->setnx(md5($key."lock"),$value);//when setnx reids will return true so we can judge whether sucess to lock it
        if(!empty($status)||$this->redis->get($key."lock") <microtime(true)&& $this->redis->getSet($key."lock", $value) <microtime(true)){
            $this->redis_lock[]=$key;
        }
        else{
            usleep(5);
            $this->try_lock_cache($key,$time);
        }
    }
    private function unlock_cache($key){
        $this->redis->del($key."lock");
        $index = array_keys($this->redis_lock,$key);
        foreach ($index as $value) {
            unset($this->redis_lock[$value]);
        }//try unlock file
    }
    public  function lock_set_cache($key,$value,$expire,$is_serialize=false){
        if(is_numeric($expire)){
            $expire=$expire+time();
        }
        if ($this->driver=="file")
        {
            if(!$is_serialize) {
                $this->file->safy_write($this->path . md5($key) . ".txt", json_encode(["value" => $value, "expire" =>$expire,"key"=>$key]));//defalut
            }
            else{
                $this->file->safy_write($this->path . md5($key) . ".txt", serialize(["value" => $value, "expire" =>$expire,"key"=>$key]));//if you want to store a object or mrthod use set_cache(($key,$value,$expire,$is_serialize=true)
            }
        }
        if($this->driver=="redis")
        {
            if(!$is_serialize) {
                if(in_array($key,$this->redis_lock)){
                    $this->redis->hSet($this->path, md5($key), json_encode(["value" => $value, "expire" =>  $expire, "key" => $key]));
                    $this->unlock_cache($key);
                }
                else{
                    if($this->redis->exists(md5($key."lock"))){
                        new Exception("403","this_cache_has_been_lock");
                    }
                    else{
                        $this->redis->hSet($this->path, md5($key), json_encode(["value" => $value, "expire" => $expire, "key" => $key]));
                        $this->unlock_cache($key);
                    }
//                    if($this->redis->hExists($this->path,md5($key))){
//                        if($this->redis->hGet("cache_lock",md5($key))-microtime(true)>0){
//                            new Exception("403","this_cahce_has_been_lock");
//                        }
//                        else{
//                            $this->redis->hSet($this->path, md5($key), json_encode(["value" => $value, "expire" => time() + $expire, "key" => $key]));
//                            $this->unlock_cache($key);
//                        }
//                    }
//                    else{
//                        $this->redis->hSet($this->path, md5($key), json_encode(["value" => $value, "expire" => time() + $expire, "key" => $key]));
//                        $this->unlock_cache($key);
//                    }
                }
            }
            else{
                if(in_array($key,$this->redis_lock)){
                    $this->redis->hSet($this->path, md5($key), serialize(["value" => $value, "expire" =>  $expire, "key" => $key]));
                    $this->unlock_cache($key);
                }//when wirete new content process will unlock this cache
                else {
                    if ($this->redis->exists(md5($key . "lock"))) {
                        new Exception("403", "this_cache_has_been_lock");
                    } else {
                        $this->redis->hSet($this->path, md5($key), serialize(["value" => $value, "expire" => $expire, "key" => $key]));
                        $this->unlock_cache($key);
                    }
                }
            }
        }
    }
    public function __destruct()
    {
        foreach ($this->redis_lock as $value){
            $this->unlock_cache($value);
        }//when end of process unlock all cache which has been locked in this process
    }
}