<?php
/*update_at 2019-10-17 09:10:15
*create_by awesome-jj
*/
namespace load;
use http;
use controller;
require_once __DIR__."/"."provider.php";
use system;
use request;
use db;
class provider_register extends provider
{
    protected static $object;
    protected function __construct()
    {

    }
    public static function provider(){
        if(is_null(self::$object)){
            self::$object=new self();
        }
        return self::$object;
    }
    protected  $middleware=[
        "sql_middleware"=>http\middleware\sql_middleware\sql_middleware::class,
        "user_token_middleware"=>http\middleware\user\user_token_middleware::class,
        "basic_middleware"=>http\middleware\basic_middleware::class,
        "limit_flow_middleware"=>http\middleware\limit\limit_flow_middleware::class,
        "csrf_middleware"=>http\middleware\csrf\csrf_middleware::class,

    ];
    protected  $controller=[
        "post_controller"=>controller\post\post_controller::class,
        "park_controller"=>controller\park\park_controller::class,
        "admin_user_controller"=>controller\admin_user\admin_user_controller::class,
        "wechat_controller"=>controller\wechat\wechat_controller::class,
        "native_controller"=>controller\native\native_controller::class,
        "pay_controller"=>controller\pay\pay_controller::class,
        "categories_controller"=>controller\shop\categories_controller::class,
        "shop_controller"=>controller\shop\shop_controller::class,
        "goods_controller"=>controller\shop\goods_controller::class,
        "map_controller"=>controller\map\map_controller::class,
        "survey_controller"=>controller\survey\survey_controller::class,
        "code_controller"=>controller\code\code_controller::class,
        "auth_controller"=>controller\auth\auth_controller::class,
        "system_controller"=>controller\system\system_controller::class,
        "index_controller"=>controller\index\index_controller::class,
        "test_controller"=>controller\test\test_controller::class,

    ];
    protected $dependencies=[
        "file"=>system\file::class,
        "code"=>system\code::class,
        "cache_"=>system\cache\cache_::class,
        "cache"=>system\cache\cache::class,
        "queue"=>system\config\queue::class,
        "http"=>system\http::class,
        "common"=>system\common::class,
        "encrypt"=>system\encrypt::class,
        "awesome"=>system\awesome::class,
        "alipay"=>system\pay\alipay::class,
        "mail"=>system\mail::class,
        "log"=>system\log::class,
        "service"=>system\config\service::class,
        "pay"=>system\config\pay::class,
        "service_config"=>system\config\service_config::class,
        "timed_task_config"=>system\config\timed_task_config::class,
        "config"=>system\config\config::class,
        "cookie"=>system\cookie::class,
        "redis"=>system\driver\cache\redis::class,
        "template"=>system\template::class,
        "class_define"=>system\class_define::class,
        "token"=>system\token::class,
        "vertify_code"=>system\vertify_code::class,
        "session"=>system\session::class,
        "upload_file"=>system\upload_file::class,
        "system_excu"=>system\system_excu::class,
        "excel"=>system\excel::class,
        "Exception"=>system\Exception::class,
        "user_login_request"=>request\user\user_login_request::class,
        "user_register_request"=>request\user\user_register_request::class,
        "vode_survey_request"=>request\survey\vode_survey_request::class,
        "request"=>request\request::class,
        "db"=>db\db::class,
        "model"=>db\model\model::class,
        "model_auto"=>db\model\model_auto\model_auto::class,
        "map"=>db\model\park\map::class,
        "oder_park"=>db\model\park\oder_park::class,
        "user"=>db\model\user\user::class,
        "admin_user_new"=>db\model\admin_user_new\admin_user_new::class,
        "goods"=>db\model\shop\goods::class,
        "categories"=>db\model\shop\categories::class,
        "comment_list"=>db\model\comment_list\comment_list::class,
        "news"=>db\factory\migration\migration_list\news::class,
        "testdasda"=>db\model\test\testdasda::class,
        "test"=>db\model\test\test::class,
        "soft_db"=>db\factory\soft_db::class,
        "migration"=>db\factory\migration\migration::class,
        "migration_comment_likes"=>db\factory\migration\migration_list\migration_comment_likes::class,
        "migration_user"=>db\factory\migration\migration_list\migration_user::class,
        "migration_park_comment"=>db\factory\migration\migration_list\migration_park_comment::class,
        "migration_notify_list"=>db\factory\migration\migration_list\migration_notify_list::class,
        "migration_comment_list"=>db\factory\migration\migration_list\migration_comment_list::class,
        "migration_titang_theme"=>db\factory\migration\migration_list\migration_titang_theme::class,
        "migration_survey"=>db\factory\migration\migration_list\migration_survey::class,
        "migrate_goods"=>db\factory\migration\migration_list\migrate_goods::class,
        "migration_admin_user"=>db\factory\migration\migration_list\migration_admin_user::class,
        "migration_order_park"=>db\factory\migration\migration_list\migration_order_park::class,
        "migration_park_list"=>db\factory\migration\migration_list\migration_park_list::class,
        "migration_goods"=>db\factory\migration\migration_list\migration_goods::class,
        "migration_request_user"=>db\factory\migration\migration_list\migration_request_user::class,
        "migration_categories"=>db\factory\migration\migration_list\migration_categories::class,
];
}