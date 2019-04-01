<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/18 0018
 * Time: 上午 9:39
 */

namespace request\user;
use request\request;

class user_login_request extends request
{
    protected $rules=[
        "name"=>"required:post",
        "password"=>"required:post|min:1|max:12",
        "code"=>"required:post"
    ];
}