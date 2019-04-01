<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5 0005
 * Time: 下午 5:09
 */

namespace system;


class file
{
    private $arr=array();
    private $path;
    public function get_file_list($dir){
        if($this->path!=$dir)
        if(@$handle = opendir($dir)) { //注意这里要加一个@，不然会有warning错误提示：）
            while(($file = readdir($handle)) !== false) {
                if($file != ".." && $file != ".") {
                    if(is_dir($dir."/".$file)) {
                        $files[] =$this->get_file_list($dir.$file);
                    } else {
                        $files[] = $file;
                        $this->arr[]=str_replace("//","/",$dir."/".$file);
                    }
                }
            }
            closedir($handle);
            $file_list=json_decode(json_encode($this->arr),true);
            return $file_list;
        }
    }//please don't direct call method,use file_walk!
    public function file_walk($path){
        $file_list=$this->get_file_list($path);
        $this->arr=[];
        return $file_list;
    }
    public function base64_jpeg($base64,$path,$name){
        $file1="/var/www/html/image/".date('Y-m-d h:i:s').".txt";
        $fp = fopen($file1, 'w');
        fwrite($fp,$base64);
        fclose($fp);
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
            $type = $result[2];
            $new_file = $path;
            if(!file_exists($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0700);
            }
            $new_file = $new_file.$name.".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64)))){
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public function read_file($path){
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $data=fread($myfile,filesize($path));
        fclose($myfile);
        return $data;
    }
    public function write_file($path,$data){
        echo $path;
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
    }
}