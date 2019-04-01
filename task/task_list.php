<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/18 0018
 * Time: 下午 6:54
 */
use system\mail;


class task_list
{
    private $arr;
    public function __construct($arr)
    {
        $this->arr=$arr;
    }
    public function send_email(){
        mail::send_email($this->arr["user_email"],$this->arr["data"],$this->arr["subject"]);
    }
}