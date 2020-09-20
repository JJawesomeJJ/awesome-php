<?php


namespace system\cache\abstract_\driver;


use system\cache\abstract_\CacheDriver;
use system\config\config;
use system\file;
use system\lock;

class CacheDriverFile extends CacheDriver
{
    protected $path;
    protected static $locked=[];
    protected $lock_path;
    public function __construct()
    {
        $this->path=config::env_path().'/filesystem/cache/';
        $this->lock_path=config::env_path().'/filesystem/lock/';
        if(!is_dir($this->path)){
            $this->mkdir($this->path,'0777');
        }
        if(!is_dir($this->lock_path)){
            $this->mkdir($this->lock_path,'0777');
        }
    }
    protected function mkdir(string $path,int $priv){
        if(is_dir($path)){
            return;
        }else{
            if(!is_dir(dirname($path))){
                $this->mkdir(dirname($path),$priv);
            }
            mkdir($path,0777);
        }
    }
    public function set(string $key, $value, $expire = "forever")
    {
        file_put_contents($this->get_file_path($key), json_encode(["key" => $key, "created_at" => time(), "expire" => $expire, "value" => $value]));
    }
    protected function get_file_path(string $key){
        return $this->path.md5($key).".txt";
    }
    public function get(string $key)
    {
        $path=$this->get_file_path($key);
        if(!is_file($path)) {
            return null;
        }
        $data=json_decode(file_get_contents($path),true);
        if($this->is_expire($data['created_at'],$data['expire'])){
            $this->del($key);
            return null;
        }
        return $data['value'];
    }
    public function getorset(string $key, \Closure $fun, $expire = "forever")
    {
        $data=$this->get($key);
        if(!is_null($data)){
            return $data;
        }
        $data=app()->make_closure($fun);
        $this->set($key,$data,$expire);
        return $data;
    }
    public function exist(string $key): bool
    {
        if(is_null($this->get($key))){
            return false;
        }
        return true;
    }
    private function get_file_list($dir,$except){
        $dir=str_replace("//","/",$dir);
        $arr=[];
        if(in_array($dir,$except))
        {
            return [];
        }
        if(is_file($dir)){
            return [$dir];
        }
        if(@$handle = opendir($dir)) { //注意这里要加一个@，不然会有warning错误提示：）
            while(($file = readdir($handle)) !== false) {
                if($file != ".." && $file != ".") {
                    if(is_dir($dir."/".$file)) {
                        $arr[]=array_merge($arr,$this->get_file_list($dir."/".$file,$except));
                    } else {
                        $arr[]=str_replace("//","/",$dir."/".iconv('GB2312', 'UTF-8',$file));
                    }
                }
            }
            closedir($handle);
            return $arr;
        }
        return [];
    }//please don't direct call method,use file_walk!
    public function get_all(): array
    {
        $result=[];
        foreach ($this->get_file_list($this->path,[]) as $item){
            $data=json_encode(file_get_contents($item));
            if($this->is_expire($data['created_at'],$data['expire'])){
                unlink($item);
            }else{
                $result[$data['key']]=$data['value'];
            }
        }
        return $result;
    }
    public function del_all()
    {
        foreach ($this->get_file_list($this->path,[]) as $item){
            unlink($item);
        }
    }
    public function lock(string $key, int $timeout, int $max_fail_timeout)
    {
        $path=$this->lock_path.md5($key).'.txt';
        $fd=fopen($path,'w+');
        while (flock($fd,LOCK_EX)==false){
            usleep(5);
        }
//        self::$locked[$path]=$fd;
    }
    public function unlock(string $key)
    {
        $path=$this->lock_path.md5($key).'.txt';
        if(array_key_exists($path,self::$locked)){
//            fclose(self::$locked[$path]);
            flock(self::$locked[$path], LOCK_UN);
            unset(self::$locked[$path]);
        }
    }
    public function __destruct()
    {
//       foreach (self::$locked as $key=>$fd){
//           flock($fd, LOCK_UN);
//       }
    }
    public function del(string $key)
    {
        unlink($this->get_file_path($key));
    }
    public function decrBy(string $key, $expire = "forever", int $default = 1, $decr_value=1)
    {
        $path=$this->get_file_path($key);
//        $this->lock($key,1,1);
        lock::redis_lock($path,1,2);
        if(!is_file($path)){
            $result=$default;
            $this->set($key,$default,$expire);
        }
        else{
            $data=json_decode(file_get_contents($path),true);
            if(!is_numeric($data['value'])){
                throw new \Exception("decrBy the cache value must be a number but ".gettype($data).' given');
            }
            $data['value']=$data['value']-$decr_value;
            $result=$data['value'];
            file_put_contents($path,json_encode($data));
        }
        lock::redis_unlock($path);
        return $result;
    }
    public function incrBy(string $key, $expire = "forever", int $default = 1, $incr_value = 1)
    {
        lock::redis_lock($key,1,2);
        $path=$this->get_file_path($key);
        if(!is_file($path)){
            $result=$default;
            $this->set($key,$default,$expire);
        }
        else{
            $data=json_decode(file_get_contents($path),true);
            if(!is_numeric($data['value'])){
                throw new \Exception("decrBy the cache value must be a number but ".gettype($data).' given');
            }
            if($this->is_expire($data['created_at'],$data['expire'])){
                $this->del($key);
                $this->set($key,$default,$expire);
                $result=$default;
            }else {
                $data['value'] = $data['value'] + $incr_value;
                $result = $data['value'];
                file_put_contents($path, json_encode($data));
            }
        }
        lock::redis_unlock($key);
        return $result;
    }
}