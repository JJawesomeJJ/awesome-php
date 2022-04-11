<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/18 0018
 * Time: 上午 11:11
 */

namespace request\user;

use request\request;

class user_register_request extends request
{
    protected $rules = [
        "name" => "required:post|min:4|max:20|unique:user",
        "password" => "required:post|min:6|max:225",
        "sex" => "required:post|accept:man,women",
        "code" => "required:post",
        "email" => "required:post|email:ture|unique:user"
    ];
}