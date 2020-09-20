<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/17 0017
 * Time: 下午 8:49
 */

namespace routes;
//the route entrance should design as resetful api style url=>resource the http request mothod as ["get","post","put","delete"]
use db\model\user\user;
use request\request;
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
routes::get("post/likes","post_controller@likes");
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
/*
 * @description 管理员界面
 */
routes::post("admin/nitify","admin_user_controller@add_websocket_nitify");
routes::get("test","test_controller@test");
routes::post("admin/theme","admin_user_controller@theme");
routes::post("admin/theme/set","admin_user_controller@set_current_theme");
routes::get("admin/theme/get","admin_user_controller@get_current_theme");
routes::get("admin/theme/list","admin_user_controller@get_theme_list");
routes::post("admin/theme/delete","admin_user_controller@delete_theme");
/**
 * @end
 */
routes::post("wechat/login","wechat_controller@login");
routes::post("park/start","park_controller@start_park");
routes::post("park/stop","park_controller@stop_park");
routes::get("park/oder","park_controller@get_oder");
routes::get("park/status","park_controller@get_current_money");
routes::get('jquery/{user_id}/{sex}',function (){
   $request=make(request::class);
   print_r($request->all());
   return runtime();
});
routes::get("pay/alipay","pay_controller@alipay");
routes::get('shop/index','shop_controller@index');
routes::get('shop/login','shop_controller@login');
routes::post('shop/request','shop_controller@get_request_info');
routes::get('shop/table','shop_controller@table');
routes::post('shop/categories','categories_controller@categories');
routes::get('shop/goods','goods_controller@import_goods');
routes::get('shop/categories/list','categories_controller@parents');
routes::any('shop/categories/children','categories_controller@get_children_catalog');
routes::post('shop/goods/num','goods_controller@goods_num');
routes::get('shop/goods/list','goods_controller@goods');
routes::get('shop/loginout','shop_controller@loginout');
routes::get('shop/goods/update','goods_controller@update');
routes::get('view',function (){
    return view("video");
});
routes::get('user/user_info','auth_controller@user_info');
routes::get('native/type','native_controller@get_native_type');
routes::post('native/start','native_controller@start')->middleware("limit_flow_middleware","ip_limit",200);
routes::get('native/online','native_controller@get_online_list');
routes::get('native/online_type','native_controller@type');
routes::get('native/banner','native_controller@banner');
routes::get('native/index',function (){
    return view("native/native_view");
});
routes::post("language","language_controller@translate");
routes::get('OAuth/add','OAuth_controller@add');
routes::get('awesome/author',"awesome_controller@author");
/**
 * @description 用户频道管理
 */
routes::post('channel/native',"channel_controller@add_native_channel");//用户加入频道
routes::post('channel/leave','channel_controller@leave_native_channel');//用户离开频道
routes::post('native/barrage',"chat_controller@send_message");
routes::get("assets/iconfont","AssetsController@IconFont");
routes::get('native/page',function (request $request,user $user){
    return view("native/barrage",[
        'user'=>$user->where("id",$request->get("playerid"))->first_cache()->get()
    ]);
});
routes::get('native/page/player',function (request $request,user $user){
    return view("native/barrage",[
        "is_show"=>false,
        'user'=>$user->where("id",$request->get("playerid"))->first_cache()->get()
    ]);
});
/**
 * @description 粉丝中心
 */
routes::post("native/gift/total","gift_controller@getGiftTotal");
routes::get('native/follow',"fans_controller@follow");
routes::get('native/fans/is','fans_controller@is_follow');
routes::get('native/fans/follow','fans_controller@follow_user');
routes::get('native/fans/unfollow','fans_controller@remove_follow');
routes::get('user/details','auth_controller@get_user_info');
routes::get('native/gift','native_controller@gifts');
routes::post('native/gift/send','gift_controller@send_gift');
routes::post('native/islike','gift_controller@is_like');
routes::post("native/like","gift_controller@like");
routes::post("native/dislike","gift_controller@dislike");
/**
 * @description CMS 后台管理系统
 */
routes::group(function (){
    routes::get("system/menu","MenuController@index");
    /**
     * 菜单设置
     */
    routes::get("system/menu/info","MenuController@menu_info");
    routes::post("system/menu/edit","MenuController@edit");
    routes::post("system/menu/add","MenuController@add_memu");
    routes::post("system/menu/del","MenuController@del");
    /**
     * 直播管理
     */
    routes::get("native/gift","NativeController@index");
    routes::get("native/online","NativeController@online");
    routes::post("native/online/num","chat_controller@get_online_num");
    routes::get("native/onlineuser","chat_controller@get_online_users_num");
    routes::post("native/giftsinfo","gift_controller@GetGifts");
    //添加礼物
    routes::post("native/gift/add","NativeController@add_gift");
    routes::post("native/gift/del","NativeController@del_gift");
    routes::get("native/gift/details","NativeController@details");
    routes::post("native/gift/edit","NativeController@edit");
    routes::get("admin/list","UserController@user_list");//用户控制器
    /**
     * @description 添加数据库
     */
    routes::get("database","DatabaseController@index");
    routes::post("database/test","DatabaseController@test");
    routes::any("native/banner","native_controller@banner_page");
},["auth_middleware"],"cms/");
/**
 * @EndDescription
 */