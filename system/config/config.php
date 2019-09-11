<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 10:47
 */

namespace system\config;


use request\request;
use system\cache\cache;
use system\common;
use system\file;

class config
{
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
    protected static $dependendcies=[
        "system",
        "request",
        "extend/test/",
        "db"
    ];
    //dependendcies_config
    //the path awesome_cli will load dependendies use php awesome load
    public static function cache(){
        return self::$cache;
    }
    public static function session(){
        return self::$session;
    }
    public static function depenendcies(){
        return self::$dependendcies;
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
        if(self::$dir_path==null){
            self::$dir_path=dirname(dirname(__DIR__)).'/';
        }
        return self::$dir_path;
    }
    public static function www_path(){
        if(self::$www_path==null){
            self::$www_path=dirname(dirname(dirname(__DIR__)))."/";
        }
        return self::$www_path;
    }
    public static function index_path(){
        if(is_cli()){
            return self::server()['host_ip'];
        }
        if(self::$index_path==null){
            $request=make('request');
            $path_info=explode('index.php',$request->get_full_url(false));
            if(count($path_info)>1){
                self::$index_path=$path_info[0]."index.php/";
            }
            else{
                self::$index_path=self::http_prefix().$_SERVER['HTTP_HOST']."/";
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
            "host"=>"127.0.0.1",
            "port"=>"6379"
        ];
    }
    public static function class_path(){
        return [
            "SuperClosure"=>"extend/SuperClosure/src",
            "PhpParser"=>"extend/SuperClosure/vendor/nikic/php-parser/lib/PhpParser",
            "PHPExcel"=>"extend/excel/PHPExcel",
            "template"=>"public/template"
        ];//define class_path
    }
    public static function server(){
        return[
           "host_ip"=>"http://www.titang.shop/"
        ];
    }
    public static function request_path(){
        if(self::$request_path==null) {
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
            "driver"=>"mysql",
            "mysql"=>[
                "hostname"=>"127.0.0.1",
                "hostport"=>"3306",
                "database"=>"",
                "username"=>"",
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
                $home_path."template/compile.php"
            ],
            //此处配置需要扫描的路径
            "except"=>[
                "/var/www/html/php/db/factory/migration"
            ]
            //如需扫描的路径下含有不希望被扫描的路径使用在此处配置
        ];
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
            "rsa_private"=>"-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQC+8n93UPCYVbG612UKEvRPlCV4QqarbpraEw9yg3gctUlylPKl
ZzafARS8OkXCa4/wIGQIfFzggIEAv3+TT+s9xMFX2eCeCSUfeBNEUn/3Vs2fgglt
FsidMQ3f82SB24P7IECuaOYI43gOiLnGRn6eN25HneAfBylYh/NYnUEXPwIDAQAB
AoGAMwdHPTGqOmucRZxOajTKiYHDybG2LNYwF9tEI4wyyyk/aZmYhs9gb3mweuTv
w5dPLhLZ6NKjV3PABd0nUMzoxnDStV+aKJls06Kwyox29gmnO/43+5a1boUOf41Q
wPZupHzr/JKpy1b3I5xSC7HBswOj9sHDzyQt4hM3Dp6ppPECQQDk0DikeUFWeoZJ
B9F7721Gnzt0tzD2aUliGAkl4vYDeUP9kINGudknz4YzL8Ewb35wG7xjIQEn1tuv
sVhrGRfdAkEA1aKCbGY4EviFIu+KnbZNTnWydwflcDHhUmjdcQkC7MY8iRZKFSEE
5Pzki5jtyi1XL6wyrzSfhjPny4ZLEVKnywJBANJvUXiqb9XJz5CA2T9jPpvRvAum
oygsQqotstQePOWK9GXSL3mvWLENYb3XsJQMJjuStppwczChoN+fedLdfB0CQQC4
qyiIoXe5VmBnyZ8OI4cB2pWxdP7tFAENJp681ihUGiw76CuTxh4f/0dkMIbkrHrg
N778WsXG0Vl+QhDj4YovAkBondDcw1w/eDexdjyDNo65O+pPillnEacXH2ulI7dY
HMcPnsFzUUxVTy5Gw+5h3OnEIYwC1oaEnaQw++C+BwRd
-----END RSA PRIVATE KEY-----",
//            非对称的的私钥
            "rsa_public"=>"-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC+8n93UPCYVbG612UKEvRPlCV4
QqarbpraEw9yg3gctUlylPKlZzafARS8OkXCa4/wIGQIfFzggIEAv3+TT+s9xMFX
2eCeCSUfeBNEUn/3Vs2fggltFsidMQ3f82SB24P7IECuaOYI43gOiLnGRn6eN25H
neAfBylYh/NYnUEXPwIDAQAB
-----END PUBLIC KEY-----"
            ];
//        非对称加密的公钥
//
//        "rsa_private"=>$file->read_file(self::home_path()."rsa_private_key.pem"),
//        "rsa_public"=>$file->read_file(self::home_path()."rsa_public_key.pem")
    }
}
