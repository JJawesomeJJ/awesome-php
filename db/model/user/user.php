<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/5 0005
 * Time: 下午 7:36
 */
namespace db\model\user;
use db\model\model;

class user extends model
{
    protected $table_name="user";
    protected $guard=["name","id","email"];
    public function comment_list(){
        return $this->has("db\model\comment_list_model","name","user_id");
    }
}