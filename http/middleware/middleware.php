<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25 0025
 * Time: 下午 6:28
 */

namespace http\middleware;


use request\request;
use system\cache\cache_;
use system\Exception;

abstract class middleware
{
    public $user_input;
    protected $request_=null;
    protected $cache_=null;
    protected $params=null;
    abstract function check();//入口
    public function __construct(request $request)
    {
        $this->request_=$request;
        $this->user_input=$this->request()->all();
        $this->xxs_filter();
        //$this->sql_filter();
        $this->check();
    }
    public function request(){
        if($this->request_==null){
            $this->request_=new request();
            return $this->request_;
        }
        else{
            return $this->request_;
        }
    }
    public function xxs_filter(){
        $black_list=array(
            "&"=>"&amp;",
            "<"=>"&lt;",
            ">"=>"&gt;",
            "”"=>"&quot;",
            "‘"=>"&#x27;",
            "/"=>"&#x2f;"
        );
        foreach ($this->user_input as $key=>$input_value)
        {
            foreach($black_list as $black_list_value=>$value)
            {
                if(strpos($input_value,$black_list_value)!==false)
                {
                    $this->user_input[$key]=str_replace($black_list_value,$value,$this->user_input[$key]);
                }
            }
        }
    }
    function sql_filter()
    {
        $black_list=[">","<","<SCRIPT>", "\\", "</SCRIPT>", "<script>", "</script>", "select", "select", "join", "join", "union", "union", "where", "where", "insert", "insert", "delete", "delete", "update", "update", "like", "like", "drop", "drop", "create", "create", "modify", "modify", "rename", "rename", "alter", "alter", "cas", "cast", "&", "&", ">", ">", "<", "<", " ", " ", "    ", "&", "'", "<br />", "''", "'", "css", "'", "CSS", "'"];
        foreach ($this->user_input as $input_value)
        {
            foreach($black_list as $black_list_value)
            {
                if(strpos(urldecode($input_value),$black_list_value)!==false)
                {
                    new Exception("403","danger_input_$input_value.$black_list_value");
                }
            }
        }
    }
    public function next(){
        $this->request()->user_input=$this->user_input;//return userinput which has been handled by this middleware
        return $this->request();
    }
    public function cache(){
        if($this->cache_==null){
            $this->cache_=new cache_();
            return $this->cache_;
        }
        else{
            return $this->cache_;
        }
    }
}