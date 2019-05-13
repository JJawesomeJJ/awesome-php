<?php
/*update_at 2019-05-02 23:25:40
*create_by awesome-jj
*/
namespace load;
use http;
use controller;

use system;
use extend;
class provider_register extends provider
{
    protected $middleware=[
    "basic_middleware"=>http\middleware\basic_middleware::class,
"sql_middleware"=>http\middleware\sql_middleware\sql_middleware::class,
"limit_flow_middleware"=>http\middleware\limit\limit_flow_middleware::class,

    ];
    protected $controller=[
     "survey_controller"=>controller\survey\survey_controller::class,
"post_controller"=>controller\post\post_controller::class,
"map_controller"=>controller\map\map_controller::class,
"code_controller"=>controller\code\code_controller::class,
"index_controller"=>controller\index\index_controller::class,
"auth_controller"=>controller\auth\auth_controller::class,
"system_controller"=>controller\system\system_controller::class,

    ];
    protected $dependencies=[
        "http"=>system\http::class,
        "Exception"=>system\Exception::class,
        "vertify_code"=>system\vertify_code::class,
        "awesome"=>system\awesome::class,
        "queue"=>system\queue::class,
        "template"=>system\template::class,
        "mail"=>system\mail::class,
        "token"=>system\token::class,
        "file"=>system\file::class,
        "config"=>system\config\config::class,
        "cache"=>system\cache\cache::class,
        "cache_"=>system\cache\cache_::class,
        "test1"=>extend\test\test1::class,
        "test4"=>extend\test\test4::class,
        "test2"=>extend\test\test2::class,
        "test3"=>extend\test\test3::class,
        "PHPMailer"=>extend\PHPMailer\PHPMailer::class,
        "SMTP"=>extend\PHPMailer\SMTP::class,
];
}