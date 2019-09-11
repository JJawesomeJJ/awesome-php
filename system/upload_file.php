<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/11 0011
 * Time: 下午 3:09
 */

namespace system;
use system\config\config;

class upload_file
{
    protected static $file_name;
    protected static $upload_file_object;
    public function __construct($file_name)
    {
        self::$file_name=$file_name;
    }
    public static function upload_file($file_name){
        if(self::$upload_file_object==null){
            self::$upload_file_object=new upload_file($file_name);
        }
        else{
            self::$file_name=$file_name;
        }
        return self::$upload_file_object;
    }
    public function isset_file(){
        if(!isset($_FILES[self::$file_name])){
            return false;
        }
        if($this->get_file_size()<=0){
            return false;
        }
        return true;
    }
    public function all_file_info(){
        return[
            "name"=>$this->get_file_name(),
            "type"=>$this->get_file_type(),
            "size"=>$this->get_file_size(),
            "tmp_name"=>$this->get_tmp_name()
        ];
    }
    public function get_file_type(){
        return $_FILES[self::$file_name]["type"];
    }
    public function get_file_name(){
        return $_FILES[self::$file_name]["name"];
    }
    public function get_file_size(){
        return $_FILES[self::$file_name]["size"];
    }
    public function get_file_error(){
        return $_FILES[self::$file_name]["error"];
    }
    public function get_tmp_name(){
        return $_FILES[self::$file_name]["tmp_name"];
    }
    public function get_file_extension(){
        $temp_arr = explode(".", $this->get_file_name());
        return array_pop($temp_arr);
    }
    public function store_upload_file($path,$rename=false,$auto_rename=true){
        if(!is_dir($path)){
            mkdir($path);
        }
        $file_name=$this->get_file_name();
        if($rename!=false){
            $file_name=$rename;
        }
        if($path[strlen($path)-1]!="/"){
            $path=$path."/";
        }
        $file_name=md5_file($_FILES[self::$file_name]['tmp_name']).".".$this->get_file_extension();
        if(file_exists($path.$file_name)){
            return str_replace(config::project_path(true),config::project_path(),$path."$file_name");
        }
        move_uploaded_file($this->get_tmp_name(), $path . $file_name);
        if(is_cli()) {
            return str_replace(config::project_path(true),"http://".config::server()["host_ip"],$path . $file_name);
        }
        else{
            return str_replace(config::project_path(true),config::project_path(),$path."$file_name");
        }
    }//相同文件只返回路径
    public function accept(array $file_extension){
        if(!in_array($this->get_file_extension(),$file_extension)){
           new Exception('403','file_type_error');
        }
        return $this;
    }
    public function max_size($max){
        if($this->get_file_size()>$max){
            new Exception('403','file_more_than_max_input');
        }
        return $this;
    }
}