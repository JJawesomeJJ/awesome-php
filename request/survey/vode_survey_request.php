<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/18 0018
 * Time: 下午 8:04
 */

namespace request\survey;


use request\request;

class vode_survey_request extends request
{
    protected $rules=[
        "answer"=>"required:post|equal:21",
        "to"=>"required:post",
    ];
}