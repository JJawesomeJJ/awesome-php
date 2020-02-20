<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5 0005
 * Time: 下午 5:09
 */

namespace system;


use Grafika\Gd\Editor;
use system\config\config;

class file
{
    private $arr=array();
    private $file_arr=[];
    protected $array_string="";
    private $fd=null;
    private $path;
    private function get_file_list($dir,$except){
        $dir=str_replace("//","/",$dir);
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
                        $files[] =$this->get_file_list($dir."/".$file,$except);
                    } else {
                        $files[] = $file;
                        $this->arr[]=str_replace("//","/",$dir."/".iconv('GB2312', 'UTF-8',$file));
                    }
                }
            }
            closedir($handle);
            $file_list=$this->arr;
            return $file_list;
        }
    }//please don't direct call method,use file_walk!
    //when file too many may cause out of memory!!
    public function file_walk($path,$except=[]){
        if(!is_dir($path)){
            new Exception(404,$path." not find");
        }
        $file_list=call_user_func_array([$this,"get_file_list"],[$path,$except]);
        $this->arr=[];
        return $file_list;
    }
    //get path all file include son dir
    public function base64_jpeg($base64,$path,$name){
//        $file1=config::upload_path("txt").date('Y-m-d h:i:s').".txt";
//        $fp = fopen($file1, 'w');
//        fwrite($fp,$base64);
//        fclose($fp);
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
            $type = $result[2];
            $new_file = $path;
            if(!is_dir($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                $this->mkdir($new_file, 0700);
            }
//            print_r($new_file);die();
            $new_file = $new_file.$name.".{$type}";
            $new_file=str_replace("\\",'/',$new_file);
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64)))){
                return str_replace("/index.php","",str_replace(config::env_path()."public",config::index_path(),$new_file));
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public function mkdir(string $path,int $priv){
        if(is_dir($path)){
            return;
        }else{
            if(!is_dir(dirname($path))){
                $this->mkdir(dirname($path),$priv);
            }
            mkdir($path,0777);
        }
    }
    //base64 to img file
    public function read_file($path){
        $path=iconv('utf-8', 'gbk', $path);
        if(!file_exists($path)){
            return false;
        }
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $data=file_get_contents($path);
        return $data;
    }//读取文件
    public function img_base64($img_path){
        if(!file_exists($img_path)){
            return false;
        }
        if($fp = fopen($img_path,"rb", 0))
        {
            $gambar = fread($fp,filesize($img_path));
            fclose($fp);
            $base64 = chunk_split(base64_encode($gambar));
            $encode = "data:image/jpg/png/gif;base64,$base64";
            return $encode;
        }
        return false;
    }//文件转化为base64
    public function write_file($path,$data){
        $name_list=explode("/",$path);
        $path_=str_replace($name_list[count($name_list)-1],"",$path);
        if(!file_exists($path_))
        if(!is_dir($path_))
        {
            mkdir($path_,0777,true);
        }
        $fd = fopen($path,"w");
        $result = fwrite($fd,$data);
        fclose($fd);
    }//写文件
    public function get_file_name($path,$extension=false){
        $path=iconv('utf-8', 'gbk', $path);
        if(!is_file($path)){
            new Exception(404,$path." not find");
        }
        if($extension){
            return iconv('GB2312', 'UTF-8',basename($path));
        }
        else{
            return iconv('GB2312', 'UTF-8',explode('.',basename($path))[0]);
        }
    }
    public function safy_read_file($path){
        if(!file_exists($path)){
            return false;
        }
        if(!is_null($this->fd)){
            $this->unlock_cache($this->fd);
            $index = array_keys($this->file_arr,$this->fd);
            foreach ($index as $value) {
                unset($this->file_arr[$value]);
            }//try unlock file
            //if read_file_just_now_and_this_file_has_been_lock_so_try_to_unlock_it
        }
        $myfile = fopen($path, "r+") or die("Unable to open file!");
        $this->fd=$myfile;
        $this->file_arr[]=$myfile;
        flock($myfile,LOCK_EX);
        $data="";
        while(!feof($myfile)) {
            $data.=fgetc($myfile);
        }//set the point of file ponit to start of file;
        return $data;
    }
    //this file has been locked so you can use write it or unlock it
    public function safy_write($key,$data){
        if($this->fd!=null) {
            ftruncate($this->fd,0);         // 将文件截断到给定的长度
            rewind($this->fd);
            $result = fwrite($this->fd, $data);
            fclose($this->fd);
            $index = array_keys($this->file_arr,$this->fd);
            foreach ($index as $value) {
                unset($this->file_arr[$value]);
            }//try unlock file
            $this->fd=null;
        }
        else{
            $this->write_file($key,$data);
        }
    }
    public function unlock_cache($fd){
        flock($fd,LOCK_UN);
        fclose($fd);
    }
    public function delete_file($path){
        $path=iconv('utf-8', 'gbk', $path);
        if(file_exists($path))
        {
            unlink($path);
        }
    }
    public function __destruct()
    {
        try {
            foreach ($this->file_arr as $value) {
                flock($value, LOCK_UN);
            }
        }
        catch (\Exception $exception){
            //echo $exception;
        }
        catch (\Error $error){
            //echo $error;
        }
        //check the file whether locked if true unlock it
    }
}