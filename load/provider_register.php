<?php
/*update_at 2019-04-10 10:10:55;
*create_by awesome-jj
*/
namespace load;
use http;
use controller;
use system;
class provider_register extends provider
{
    protected $middleware=[
    "basic_middleware"=>http\middleware\basic_middleware::class,
"sql_middleware"=>http\middleware\sql_middleware\sql_middleware::class,

    ];
    protected $controller=[
"survey_controller"=>controller\survey\survey_controller::class,
"post_controller"=>controller\post\post_controller::class,
"map_controller"=>controller\map\map_controller::class,
"code_controller"=>controller\code\code_controller::class,
"auth_controller"=>controller\auth\auth_controller::class,
"system_controller"=>controller\system\system_controller::class,
    ];
    protected $dependencies=[
        "http.php"=>system\http::class,
        "Exception.php"=>system\Exception::class,
        "vertify_code.php"=>system\vertify_code::class,
        "awesome.php"=>system\awesome::class,
        "queue.php"=>system\queue::class,
        "template.php"=>system\template::class,
        "mail.php"=>system\mail::class,
        "token.php"=>system\token::class,
        "file.php"=>system\file::class,
        "config.php"=>system\config\config::class,
        "cache.php"=>system\cache\cache::class,
        "cache_.php"=>system\cache\cache_::class,
        "survey_html_create.php"=>controller\survey\survey_html_create::class,
        "survey_controller.php"=>controller\survey\survey_controller::class,
        "post_controller.php"=>controller\post\post_controller::class,
        "controller.php"=>controller\controller::class,
        "map_controller.php"=>controller\map\map_controller::class,
        "code_controller.php"=>controller\code\code_controller::class,
        "index_controller.php"=>controller\index\index_controller::class,
        "auth_controller.php"=>controller\auth\auth_controller::class,
        "system_controller.php"=>controller\system\system_controller::class,
    ];
}