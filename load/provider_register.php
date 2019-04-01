<?php
/*update_at 2019-03-31 02:21:58;
*create_by awesome-jj
*/
namespace load;
use http;
use controller;

class provider_register extends provider
{
    protected $middleware=[
    "basic_middleware"=>http\middleware\basic_middleware::class,
"sql_middleware"=>http\middleware\sql_middleware\sql_middleware::class,

    ];
    protected $controller=[
     "survey_controller"=>controller\survey\survey_controller::class,
"map_controller"=>controller\map\map_controller::class,
"code_controller"=>controller\code\code_controller::class,
"auth_controller"=>controller\auth\auth_controller::class,

    ];
}