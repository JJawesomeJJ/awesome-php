<?php
/*update_at 2020-01-06 21:13:13
*create_by awesome-jj
*/
namespace load;
use http;
require_once __DIR__."/"."provider.php";
use system;
use request;
use db;
use app;
class provider_register extends provider
{
    protected static $object;
    protected function __construct()
    {
        parent::__construct();
    }
    public static function provider(){
        if(is_null(self::$object)){
            self::$object=new self();
        }
        return self::$object;
    }
    protected  $middleware=[
        "basic_middleware"=>http\middleware\basic_middleware::class,
        "csrf_middleware"=>http\middleware\csrf\csrf_middleware::class,
        "limit_flow_middleware"=>http\middleware\limit\limit_flow_middleware::class,
        "sql_middleware"=>http\middleware\sql_middleware\sql_middleware::class,

    ];
    protected  $controller=[
        "admin_user_controller"=>app\controller\admin_user\admin_user_controller::class,
        "auth_controller"=>app\controller\auth\auth_controller::class,
        "code_controller"=>app\controller\code\code_controller::class,
        "index_controller"=>app\controller\index\index_controller::class,
        "language_controller"=>app\controller\language\language_controller::class,
        "map_controller"=>app\controller\map\map_controller::class,
        "native_controller"=>app\controller\native\native_controller::class,
        "park_controller"=>app\controller\park\park_controller::class,
        "pay_controller"=>app\controller\pay\pay_controller::class,
        "post_controller"=>app\controller\post\post_controller::class,
        "categories_controller"=>app\controller\shop\categories_controller::class,
        "goods_controller"=>app\controller\shop\goods_controller::class,
        "shop_controller"=>app\controller\shop\shop_controller::class,
        "survey_controller"=>app\controller\survey\survey_controller::class,
        "system_controller"=>app\controller\system\system_controller::class,
        "test_controller"=>app\controller\test\test_controller::class,
        "wechat_controller"=>app\controller\wechat\wechat_controller::class,

    ];
    protected $dependencies=[
        "awesome"=>system\awesome::class,
        "cache"=>system\cache\cache::class,
        "cache_"=>system\cache\cache_::class,
        "class_define"=>system\class_define::class,
        "code"=>system\code::class,
        "common"=>system\common::class,
        "channel_config"=>system\config\channel_config::class,
        "config"=>system\config\config::class,
        "pay"=>system\config\pay::class,
        "queue"=>system\queue::class,
        "service"=>system\config\service::class,
        "service_config"=>system\config\service_config::class,
        "timed_task_config"=>system\config\timed_task_config::class,
        "cookie"=>system\cookie::class,
        "redis"=>system\driver\cache\redis::class,
        "encrypt"=>system\encrypt::class,
        "excel"=>system\excel::class,
        "Exception"=>system\Exception::class,
        "file"=>system\file::class,
        "http"=>system\http::class,
        "Channel"=>system\kernel\Channel\Channel::class,
        "Event"=>system\kernel\event\Event::class,
        "EventListener"=>system\kernel\event\EventListener::class,
        "event_system"=>system\kernel\event\event_system::class,
        "facede"=>system\kernel\facede::class,
        "ServiceProvider"=>app\ServiceProvider::class,
        "lock"=>system\lock::class,
        "log"=>system\log::class,
        "mail"=>system\mail::class,
        "alipay"=>system\pay\alipay::class,
        "session"=>system\session::class,
        "system_excu"=>system\system_excu::class,
        "template"=>system\template::class,
        "token"=>system\token::class,
        "upload_file"=>system\upload_file::class,
        "vertify_code"=>system\vertify_code::class,
        "request"=>request\request::class,
        "response"=>request\response::class,
        "user_login_request"=>request\user\user_login_request::class,
        "user_register_request"=>request\user\user_register_request::class,
        "db"=>db\db::class,
        "migration"=>db\factory\migration\migration::class,
        "migration_admin_user"=>db\factory\migration\migration_list\migration_admin_user::class,
        "migration_categories"=>db\factory\migration\migration_list\migration_categories::class,
        "migration_comment_likes"=>db\factory\migration\migration_list\migration_comment_likes::class,
        "migration_comment_list"=>db\factory\migration\migration_list\migration_comment_list::class,
        "migration_goods"=>db\factory\migration\migration_list\migration_goods::class,
        "migration_native"=>db\factory\migration\migration_list\migration_native::class,
        "migration_notify_list"=>db\factory\migration\migration_list\migration_notify_list::class,
        "migration_order_park"=>db\factory\migration\migration_list\migration_order_park::class,
        "migration_park_comment"=>db\factory\migration\migration_list\migration_park_comment::class,
        "migration_park_list"=>db\factory\migration\migration_list\migration_park_list::class,
        "migration_request_user"=>db\factory\migration\migration_list\migration_request_user::class,
        "migration_survey"=>db\factory\migration\migration_list\migration_survey::class,
        "migration_titang_theme"=>db\factory\migration\migration_list\migration_titang_theme::class,
        "migration_user"=>db\factory\migration\migration_list\migration_user::class,
        "news"=>db\model\news\news::class,
        "soft_db"=>db\factory\soft_db::class,
        "admin_user_new"=>db\model\admin_user_new\admin_user_new::class,
        "comment_list"=>db\model\comment_list\comment_list::class,
        "model"=>db\model\model::class,
        "model_auto"=>db\model\model_auto\model_auto::class,
        "map"=>db\model\park\map::class,
        "oder_park"=>db\model\park\oder_park::class,
        "categories"=>db\model\shop\categories::class,
        "goods"=>db\model\shop\goods::class,
        "test"=>db\model\test\test::class,
        "testdasda"=>db\model\test\testdasda::class,
        "user"=>db\model\user\user::class,
        "admin_user_controller"=>app\controller\admin_user\admin_user_controller::class,
        "auth_controller"=>app\controller\auth\auth_controller::class,
        "code_controller"=>app\controller\code\code_controller::class,
        "controller"=>app\controller\controller::class,
        "index_controller"=>app\controller\index\index_controller::class,
        "language_controller"=>app\controller\language\language_controller::class,
        "map_controller"=>app\controller\map\map_controller::class,
        "native_controller"=>app\controller\native\native_controller::class,
        "tencent_clound"=>app\controller\native\tencent_clound::class,
        "park_controller"=>app\controller\park\park_controller::class,
        "pay_controller"=>app\controller\pay\pay_controller::class,
        "post_controller"=>app\controller\post\post_controller::class,
        "categories_controller"=>app\controller\shop\categories_controller::class,
        "goods_controller"=>app\controller\shop\goods_controller::class,
        "shop"=>app\controller\shop\shop::class,
        "shop_controller"=>app\controller\shop\shop_controller::class,
        "survey_controller"=>app\controller\survey\survey_controller::class,
        "survey_html_create"=>app\controller\survey\survey_html_create::class,
        "system_controller"=>app\controller\system\system_controller::class,
        "test_controller"=>app\controller\test\test_controller::class,
        "wechat_controller"=>app\controller\wechat\wechat_controller::class,
        "user_login_event"=>app\Event\user_login_event::class,
        "user_login_listener"=>app\EventListener\user_login_listener::class,
        "AppServiceProvider"=>app\providers\AppServiceProvider::class,
        "EventServiceProvider"=>app\providers\EventServiceProvider::class,
];
}