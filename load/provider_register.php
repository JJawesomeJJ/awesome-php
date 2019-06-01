<?php
/*update_at 2019-05-24 22:47:44
*create_by awesome-jj
*/
namespace load;
use http;
use controller;

use system;
use request;
use extend;
class provider_register extends provider
{
    protected $middleware=[
    "sql_middleware"=>http\middleware\sql_middleware\sql_middleware::class,
"basic_middleware"=>http\middleware\basic_middleware::class,
"limit_flow_middleware"=>http\middleware\limit\limit_flow_middleware::class,

    ];
    protected $controller=[
     "post_controller"=>controller\post\post_controller::class,
"admin_user_controller"=>controller\admin_user\admin_user_controller::class,
"map_controller"=>controller\map\map_controller::class,
"survey_controller"=>controller\survey\survey_controller::class,
"code_controller"=>controller\code\code_controller::class,
"auth_controller"=>controller\auth\auth_controller::class,
"system_controller"=>controller\system\system_controller::class,
"index_controller"=>controller\index\index_controller::class,

    ];
    protected $dependencies=[
        "file"=>system\file::class,
        "cache_"=>system\cache\cache_::class,
        "cache"=>system\cache\cache::class,
        "queue"=>system\config\queue::class,
        "http"=>system\http::class,
        "compile"=>system\template\compile::class,
        "awesome"=>system\awesome::class,
        "mail"=>system\mail::class,
        "timed_task_config"=>system\config\timed_task_config::class,
        "config"=>system\config\config::class,
        "template"=>system\template::class,
        "token"=>system\token::class,
        "vertify_code"=>system\vertify_code::class,
        "Exception"=>system\Exception::class,
        "user_login_request"=>request\user\user_login_request::class,
        "user_register_request"=>request\user\user_register_request::class,
        "vode_survey_request"=>request\survey\vode_survey_request::class,
        "request"=>request\request::class,
        "test4"=>extend\test\test4::class,
        "test3"=>extend\test\test3::class,
        "test1"=>extend\test\test1::class,
        "test2"=>extend\test\test2::class,
];
}