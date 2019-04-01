<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25 0025
 * Time: 下午 6:28
 */

namespace http\middleware;


use system\Exception;

abstract class middleware
{
    public $user_input;
    abstract function check();//入口
    public function __construct()
    {
        $this->user_input=$this->get_user_input();
        $this->xxs_filter();
        $this->sql_filter();
        $this->check();
    }
    public function get_user_input(){
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            $parmas_list=[];
            $url=$_SERVER['REQUEST_URI'];
            $url_data=explode("?",$url);
            $get_params=explode("&",$url_data[1]);
            foreach ($get_params as $value)
            {
                $parms_key_value=explode('=',$value);
                $parmas_list[$parms_key_value[0]]=$parms_key_value[1];
            }
            return $parmas_list;
        }
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $parms=file_get_contents("php://input");
            $get_params=explode("&",$parms);
            foreach ($get_params as $value)
            {
                $parms_key_value=explode('=',$value);
                $parmas_list[$parms_key_value[0]]=$parms_key_value[1];
            }
            return $parmas_list;
        }
    }
    public function xxs_filter(){
        $black_list=array(
            "&"=>"&amp;",
            "<=>&lt;",
            ">"=>"&gt;",
            "”"=>"&quot;",
            "‘"=>"&#x27;",
            "/"=>"&#x2f;"
        );
        foreach ($this->user_input as $input_value)
        {
            foreach($black_list as $black_list_value=>$value)
            {
                if(strpos(urldecode($input_value),$black_list_value)!==false)
                {
                    new Exception("403","danger_input_$input_value");
                }
            }
        }
    }
    function sql_filter()
    {
        $black_list=["\\", "\\",">","<","<SCRIPT>", "\\", "</SCRIPT>", "<script>", "</script>", "select", "select", "join", "join", "union", "union", "where", "where", "insert", "insert", "delete", "delete", "update", "update", "like", "like", "drop", "drop", "create", "create", "modify", "modify", "rename", "rename", "alter", "alter", "cas", "cast", "&", "&", ">", ">", "<", "<", " ", " ", "    ", "&", "'", "<br />", "''", "'", "css", "'", "CSS", "'"];
        foreach ($this->user_input as $input_value)
        {
            foreach($black_list as $black_list_value)
            {
                if(strpos(urldecode($input_value),$black_list_value)!==false)
                {
                    new Exception("403","danger_input_$input_value");
                }
            }
        }

    }
}