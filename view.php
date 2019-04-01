<?php
namespace view;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/10 0010
 * Time: 下午 10:36
 */
class view{
    public function __construct($path)
    {
    }

    public function extend($content,$perent){
        str_replace("@extend",$perent,$content);
        return $content;
    }
    public function red_file($path){
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $data_content=fread($myfile,filesize("webdictionary.txt"));
        fclose($myfile);
        return $data_content;
    }
}