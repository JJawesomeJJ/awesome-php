<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 8:49
 */

namespace routes;
use auth_controller;
use survey_controller;
use http\middleware;

class route_entrance extends routes
{
    protected $routes=[
        [
            "post","user/login","auth_controller","user_login","basic_middleware"
        ],
        [
            "get","user/logout","auth_controller","logout",
        ],
        [
            "post","user/register","auth_controller","user_register","basic_middleware"
        ],
        [
            "post","survey/vote","survey_controller","vote",
        ],
        [
            "post","survey/query","survey_controller","get_survey_info"
        ],
        [
            "post","survey/draw","survey_controller","draw_survey"
        ],
        [
            "post","survey/delete","survey_controller","delete_database_file"
        ],
        [
            "post","survey/create","survey_controller","template_page_create"
        ],
        [
            "post","map/upload_park_message","map_controller","upload_park_message"
        ],
        [
            "get","map/query_coordinate","map_controller","adress_to_coordinate","basic_middleware"
        ],
        [
            "get","map/message_manage","map_controller","map_message_manage"
        ],
        [
            "get","map/message_manage/check_pass","map_controller","check_pass"
        ],
        [
            "get","map/admin_login","auth_controller","admin_login"
        ],
        [
            "get","map/user_get_park","map_controller","user_get_park"
        ],
        [
            "get","code/email_code","code_controller","map_admin_email"
        ]
    ];
}