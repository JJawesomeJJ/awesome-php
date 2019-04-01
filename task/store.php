<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/6 0006
 * Time: 下午 5:23
 */

use system\file;
class store
{
    private $arr;
    public function __construct($arr)
    {
        $this->arr=$arr;
    }
    public function store_base64(){
        $file=new file();
        $arr=$this->arr;
        $file->base64_jpeg($arr["base64"],$arr["path"],$arr["name"]);
    }
}