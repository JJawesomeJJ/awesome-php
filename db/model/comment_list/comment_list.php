<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/5 0005
 * Time: 下午 7:39
 */

namespace db\model\comment_list;


use db\model\model;

class comment_list extends model
{
    protected $table_name="comment_list";
    protected $guard=["id","user_id"];
}