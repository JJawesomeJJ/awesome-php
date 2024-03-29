<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 10:47
 */

namespace system\config;


use app\providers\AppServiceProvider;
use app\providers\EventServiceProvider;
use app\providers\MiddlewareProvider;
use request\request;
use system\cache\cache;
use system\common;
use system\file;

class config
{
    protected static $cli;
    protected static $dir_path;
    protected static $home_path;
    protected static $www_path;
    protected static $request_path;
    protected static $index_path;
    protected static $project_path;
    protected static $cache=[
        "driver"=>"file",//you can choose faster driver like redis
        "path"=>"filesystem/cache"//defalut path
    ];
    //we support driver redis and file until now may we will provide new driver but writer just a college student no time to do this; cretae_at_2019/4/11/ 9:24
    //cache config
    protected static $session=[
        "name"=>"ssid",
        "driver"=>"redis",//you can choose faster driver like redis
        "path"=>"filesystem\\session\\",//if you choose redis the path is redis HASHKEY
    ];
    //session config
    //when test redis and file as cache driver but i find when data not enough much file faster than redis write 2019/4/12
    //dependendcies_config
    //the path awesome_cli will load dependendies use php awesome load
    public static function cache(){
        return self::$cache;
    }
    public static function session(){
        return self::$session;
    }
    public static function provider(){
        return [
            AppServiceProvider::class,
            EventServiceProvider::class,
            MiddlewareProvider::class
        ];
    }
    //the project depenendcies path
    public static function depenendcies(){
        return [
            "must"=>[
                "system",
                "request",
                "db",
                "app",
                "extend/awesome",
                "task"
            ],
            "extend"=>[
                "alipay"=>"extend/alipay/",
                "alipay_test"=>"extend/alipay_test/",
                "tencent_sdk"=>"extend/tencentcloud-sdk-php/"
            ],
            'controller'=>"app/controller/",
            'console'=>'app/console'
        ];
    }
    public static function is_cli(){
        if(is_null(self::$cli)) {
            self::$cli = preg_match("/cli/i", php_sapi_name()) ? true : false;
        }
        return self::$cli;
    }
    public static function database(){
        return [
            "type"=>"mysql",
            "hostname"=>"127.0.0.1",
            "hostport"=>"3306",
            "database"=>"",
            "username"=>"",
            "password"=>"",
        ];
    }
    public static function debug(){
        return [
            "status"=>true,
            "log_path"=>"filesystem/log/error",
            "is_notify_admin"=>false
        ];
    }
    public static function user(){
        return [
            "email"=>""
        ];
    }
    public static function home_path(){
        if(is_null(self::$home_path)){
            self::$home_path=dirname(__DIR__)."/";
        }
        return self::$home_path;
    }
    public static function env_path(){
        if(is_null(self::$dir_path)){
            self::$dir_path=str_replace("\\","/",dirname(dirname(dirname(__FILE__))).'/');
        }
        return self::$dir_path;
    }
    public static function www_path(){
        if(is_null(self::$www_path)){
            self::$www_path=dirname(dirname(dirname(__DIR__)))."/";
        }
        return self::$www_path;
    }
    public static function index_path(){
        if(!is_null(self::$index_path)){
            return self::$index_path;
        }
        if(is_cli()){
            self::$index_path=self::server()['host_ip'];
            if(isset($_SERVER['X-REAL-PORT'])) {
                self::$index_path=self::$index_path.":".$_SERVER['X-REAL-PORT'];
            }
        }else {
            if (self::$index_path == null) {
                $request = make('request');
                $path_info = explode('index.php', $request->get_full_url(false));
                if (count($path_info) > 1) {
                    self::$index_path = $path_info[0] . "index.php/";
                } else {
                    self::$index_path = self::http_prefix() . $_SERVER['HTTP_HOST'] . "/";
                }
                if(isset($_SERVER['X-REAL-PORT'])) {
                    self::$index_path=str_replace($_SERVER['HTTP_HOST'],$_SERVER['HTTP_HOST'].":".$_SERVER['X-REAL-PORT'],self::$index_path);
                }
            }
        }
        return self::$index_path;
    }
    public static function url_html_suffix(){
        return 'html';
    }
    //此处伪静态配置
    public static function project_path($abs=false){
        if($abs){
            return str_replace('\\','/',self::env_path().'public/');
        }
        if(is_null(self::$project_path)){
           self::$project_path=str_replace('index.php','',self::index_path());
        }
        return self::$project_path;
    }
    public static function task_record_list(){
        return [
            "name"=>"task_record_list",
        ];
    }
    public static function redis(){
        return [
            "host"=>"redis",
            "port"=>"6379",
            "password"=>"",
            "index"=>"10"
        ];
    }
    public static function class_path(){
        return [
            "SuperClosure"=>"extend/SuperClosure/src",
            "PhpParser"=>"extend/SuperClosure/vendor/nikic/php-parser/lib/PhpParser",
            "PHPExcel"=>"extend/excel/PHPExcel",
            "template"=>"public/template",
            "TencentCloud"=>"extend/tencentcloud-sdk-php/src/TencentCloud"
        ];//define class_path
    }
    public static function server(){
        return[
           "host_ip"=>"http://www.titang.shop"
        ];
    }
    public static function request_path(){
        if(is_null(self::$request_path)) {
            if(!is_cli()) {
                self::$request_path = "http://".$_SERVER['HTTP_HOST']."/" . explode('index.php', $_SERVER['DOCUMENT_URI']??$_SERVER['SCRIPT_NAME'])[0];
            }
            else{
                self::$request_path = "http://".self::server()['host_ip'] ."/". explode('index.php', $_SERVER['DOCUMENT_URI']??$_SERVER['SCRIPT_NAME'])[0];
            }
        }
        return self::$request_path;
    }
    public static function is_https(){
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return true;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }
    public static function pdo(){
        return[
            "EnableMasterCluster"=>false,//是否开启读写分离多实例
            "driver"=>"mysql",
            "mysql"=>[
                "hostname"=>"127.0.0.1",
                "hostport"=>"3307",
                "database"=>"",
                "username"=>"root",
                "password"=>"",
            ],
        ];
    }
    //配置本机IP地址
    public static function model_auto(){
        return [
            ""
        ];
    }
    //可以被动态实例化的模型此处设置白名单
    public static function swoole_dev(){
        $home_path=dirname(self::home_path())."/";
        return[
            "scan"=>[
                $home_path."controller",
                $home_path."db",
                $home_path."http",
                $home_path."load",
                $home_path."public",
                $home_path."routes",
                $home_path."system",
                $home_path."task",
            ],
            //此处配置需要扫描的路径
            "except"=>[
                "/var/www/html/php/db/factory/migration"
            ]
            //如需扫描的路径下含有不希望被扫描的路径使用在此处配置
        ];
    }
    public static function upload_path(string $path){
        return self::env_path()."public/upload/$path/".date("Y-m-d")."/";
    }
    public static function http_prefix(){
        if(self::is_https()){
            return 'https://';
        }
        return 'http://';
    }
    //此处配置swoole热更新所要扫描的路径文件
    public static function encrypt(){
        $file=new file();
        return [
            "aes_key"=>"19971998abc",
            //对称加密的的秘钥
            "rsa_private"=>"",
//            非对称的的私钥
            "rsa_public"=>""
            ];
//        非对称加密的公钥
//
//        "rsa_private"=>$file->read_file(self::home_path()."rsa_private_key.pem"),
//        "rsa_public"=>$file->read_file(self::home_path()."rsa_public_key.pem")
    }
}
