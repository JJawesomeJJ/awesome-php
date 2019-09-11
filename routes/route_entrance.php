<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 8:49
 */

namespace routes;
//the route entrance should design as resetful api style url=>resource the http request mothod as ["get","post","put","delete"]
use controller\park\park_controller;
use request\request;
use template\compile;

routes::post("user/login","auth_controller@user_login")->middleware("limit_flow_middleware","ip_limit",50);
routes::post("user/server","auth_controller@request_connect_websocket");
routes::get("user/head_img","auth_controller@get_head_img");
routes::get("user/logout","auth_controller@logout");
routes::post("user/register","auth_controller@user_register");
routes::post("survey/vote","survey_controller@vote");
routes::post("survey/query","survey_controller@get_survey_info");
routes::post("survey/draw","survey_controller@draw_survey");
routes::post("survey/delete","survey_controller@delete_database_file");
routes::post("survey/create","survey_controller@template_page_create");
routes::post("map/upload_park_message","map_controller@upload_park_message");
routes::get("map/query_coordinate","map_controller@adress_to_coordinate");
routes::get("map/location_to_coordinate","map_controller@location_to_city");
routes::get("map/message_manage","map_controller@map_message_manage");
routes::get("map/message_manage/check_pass","map_controller@check_pass");
routes::get("map/admin_login","auth_controller@admin_login");
routes::get("map/user_get_park","map_controller@user_get_park");
routes::get("map/instance","map_controller@get_instance");
routes::get("code/email_code","code_controller@map_admin_email");
routes::get('code/code',"code_controller@code_");
routes::get('code/qrcode',"code_controller@qrcode");
routes::post("system/notify_user","system_controller@notify_user_all");
routes::get("post/comment","post_controller@comment");
routes::post("post/reply","post_controller@reply");
routes::get("post/get_comment","post_controller@get_comment");
routes::get("xiaoer","index_controller@index");
routes::get("post/news","post_controller@get_news_content");
routes::post("user/reset","auth_controller@reset_password")->middleware('limit_flow_middleware','ip_limit',50);
routes::put("user/reset","auth_controller@update_password");
routes::get("user/reset","auth_controller@reset_password_page");
routes::get("user/forget",function (){
    return view("user/forget");
});
routes::get("user/bitch",function (){
    return view("vivo/main");
});
routes::get("user/bitch/buy",function (){
    return view("vivo/buy");
});
routes::post("admin_user/register","admin_user_controller@register");
routes::post("admin_user/login","admin_user_controller@login");
routes::any("admin_user/login/email","admin_user_controller@email_code_login");
routes::get("vertify","code_controller@img_cut_square",[["limit_flow_middleware","ip_limit","50"]]);
routes::get("vertify/slide","code_controller@slide_code");
routes::post("vertify/silde/x","code_controller@vertify_slide");
routes::get("admin/user","admin_user_controller@user_login");
routes::get("admin/user/list","admin_user_controller@get_online_user_info");
routes::get("admin/control/{service}","admin_user_controller@system_controller_pannel");
routes::post("admin/service","admin_user_controller@start_service");
routes::post("admin/service/restart","admin_user_controller@restart_service");
routes::post("admin/service/close","admin_user_controller@abort_service");
//routes::post("admin/{service}/{operate}",function (){
//
//});
routes::get("admin/service/status","admin_user_controller@get_all_service_info");
routes::get("system/notify/list","test_controller@test");
routes::get("test/{name}/{password}",function (request $request){
    $requet=make("request");
    echo $requet->get("name");
    return microtime(true)-$GLOBALS["time"];
});
routes::post("test/post",function (){
    $request=make("request");
    return $request->all();
});
routes::post("admin/nitify","admin_user_controller@add_websocket_nitify");
routes::get("test","test_controller@test");
routes::post("admin/theme","admin_user_controller@theme");
routes::post("admin/theme/set","admin_user_controller@set_current_theme");
routes::get("admin/theme/get","admin_user_controller@get_current_theme");
routes::get("admin/theme/list","admin_user_controller@get_theme_list");
routes::post("admin/theme/delete","admin_user_controller@delete_theme");
routes::post("wechat/login","wechat_controller@login");
routes::post("park/start","park_controller@start_park");
routes::post("park/stop","park_controller@stop_park");
routes::get("park/oder","park_controller@get_oder");
routes::get("park/status","park_controller@get_current_money");
