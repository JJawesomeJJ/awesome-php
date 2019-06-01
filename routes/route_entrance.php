<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 8:49
 */

namespace routes;
//the route entrance should design as resetful api style url=>resource the http request mothod as ["get","post","put","delete"]
//class route_entrance extends routes
//{
//    protected $routes=[
//        [
//            "post","user/login","auth_controller","user_login","limit_flow_middleware"
use template\compile;

routes::post("user/login","auth_controller@user_login",["limit_flow_middleware"],["limit_flow_middleware"=>["method"=>"ip_limit","time"=>50]]);
//        ],
//        [
//            "post","user/server","auth_controller","request_connect_websocket"
routes::post("user/server","auth_controller@request_connect_websocket");
//        ],
//        [
//            "get","user/head_img","auth_controller","get_head_img"
//        ],
routes::get("user/head_img","auth_controller@get_head_img");
//        [
//            "get","user/logout","auth_controller","logout",
routes::get("user/logout","auth_controller@logout");
//        ],
//        [
//            "post","user/register","auth_controller","user_register","basic_middleware"
//        ],
routes::post("user/register","auth_controller@user_register");
//        [
//            "post","survey/vote","survey_controller","vote",
routes::post("survey/vote","survey_controller@vote");
//        ],
//        [
//            "post","survey/query","survey_controller","get_survey_info"
//        ],
routes::post("survey/query","survey_controller@get_survey_info");
//        [
//            "post","survey/draw","survey_controller","draw_survey"
//        ],
routes::post("survey/draw","survey_controller@draw_survey");
//        [
//            "post","survey/delete","survey_controller","delete_database_file"
//        ],
routes::post("survey/delete","survey_controller@delete_database_file");
//        [
//            "post","survey/create","survey_controller","template_page_create"
//        ],
routes::post("survey/create","survey_controller@template_page_create");
//        [
//            "post","map/upload_park_message","map_controller","upload_park_message"
//        ],
routes::post("map/upload_park_message","map_controller@upload_park_message");
//        [
//            "get","map/query_coordinate","map_controller","adress_to_coordinate","basic_middleware"
//        ],
routes::get("map/query_coordinate","map_controller@adress_to_coordinate");
//        [
//            "get","map/message_manage","map_controller","map_message_manage"
//        ],
routes::get("map/message_manage","map_controller@map_message_manage");
//        [
//            "get","map/message_manage/check_pass","map_controller","check_pass"
//        ],
routes::get("map/message_manage/check_pass","map_controller@check_pass");
//        [
//            "get","map/admin_login","auth_controller","admin_login"
//        ],
routes::get("map/admin_login","auth_controller@admin_login");
//        [
//            "get","map/user_get_park","map_controller","user_get_park"
//        ],
routes::get("map/user_get_park","map_controller@user_get_park");
//        [
//            "get","code/email_code","code_controller","map_admin_email"
//        ],
routes::get("code/email_code","code_controller@map_admin_email");
//        [
//            "get","system/notify_user","system_controller","notify_user_all"
//        ],
routes::get("system/notify_user","system_controller@notify_user_all");
//        [
//            "get","post/comment","post_controller","comment","basic_middleware"
//        ],
routes::get("post/comment","post_controller@comment");
//        [
//            "post","post/reply","post_controller","reply"
//        ],
routes::post("post/reply","post_controller@reply");
//        [
//            "get","post/get_comment","post_controller","get_comment"
//        ],
routes::get("post/get_comment","post_controller@get_comment");
//        [
//            "get","index/","index_controller","index","limit_flow_middleware"
//        ],
routes::get("index/","index_controller@index",["limit_flow_middleware","leaky_bucket","200"]);
//        [
//            "get","post/news","post_controller","get_news_content","basic_middleware"
//        ]
routes::get("post/news","post_controller@get_news_content");
//    ];
//}
routes::post("user/reset","auth_controller@reset_password",[["limit_flow_middleware","ip_limit","50"]]);
//when you want to reset password
routes::put("user/reset","auth_controller@update_password");
//when you want
routes::get("user/reset","auth_controller@reset_password_page");
routes::get("user/forget",function (){
    $complie=new compile();
    return $complie->view("user/forget");
});
routes::get("user/bitch",function (){
    $complie=new compile();
    return $complie->view("vivo/main");
});
routes::get("user/bitch/buy",function (){
    $complie=new compile();
    return $complie->view("vivo/buy");
});
routes::post("admin_user/register","admin_user_controller@register");
routes::post("admin_user/login","admin_user_controller@login");
routes::post("admin_user/login/email","admin_user_controller@email_code_login");
routes::get("vertify","code_controller@img_cut_square",[["limit_flow_middleware","ip_limit","50"]]);
routes::get("vertify/slide","code_controller@slide_code");
routes::post("vertify/silde/x","code_controller@vertify_slide");
routes::get("admin/user","admin_user_controller@user_login");
routes::get("admin/control","admin_user_controller@system_controller_pannel");
routes::get("admin/service","admin_user_controller@start_service");
routes::get("admin/service/status","admin_user_controller@get_all_service_info");