<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/5 0005
 * Time: 下午 7:36
 */
namespace db\model\user;
use db\factory\soft_db;
use db\model\model;

class user extends model
{
    protected $table_name="user";
    protected $guard=["id","email"];
    public function comment_list(){
        return $this->has("db\model\comment_list\comment_list","name","user_id");
    }
    public function get_amount($id){
        $user=new self();
        return $user->where("id",$id)->get()->amount;
    }
}