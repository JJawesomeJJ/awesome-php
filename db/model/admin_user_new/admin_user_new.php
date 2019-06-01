<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24 0024
 * Time: 下午 2:55
 */

namespace db\model\admin_user_new;


use db\model\model;
use system\file;

class admin_user_new extends model
{
    protected $table_name="admin_user_new";
    protected $guard=["name","id","email"];
}