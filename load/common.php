<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/2 0002
 * Time: 下午 3:35
 */
$app=new \load\app();
$provider=new \load\provider_register();
//register_shutdown_function("on_app_stop");
//set_error_handler("on_app_stop", E_ALL | E_STRICT);
//set_exception_handler("on_app_stop");
function make($class_name)
{
    if(!isset($GLOBALS["provider"])){
        $GLOBALS["provider"]=new \load\provider_register();
    }
    return $GLOBALS["provider"]->make($class_name);
}
function app(){
    if(!isset($GLOBALS["app"])){
        $app=new \load\app();
        $GLOBALS["app"]=$app;
        return $app;
    }
    else{
        return $GLOBALS["app"];
    }
}
function view($path,$data=[]){
//    if(!isset($GLOBALS["compile"])){
//        $GLOBALS["compile"]=new \template\compile();
//    }
    return \template\compile_parse::compile($path,$data);
}
function redirect($path){
    header("Location: $path");
}
function make_method($method,$class_name=false){
    if(!isset($GLOBALS["provider"])){
        $GLOBALS["provider"]=new \load\provider_register();
    }
    return $GLOBALS["provider"]->make_method($method,$class_name);
}
function make_method_static($class_name,$method,$params=[]){
    if(!isset($GLOBALS["provider"])){
        $GLOBALS["provider"]=new \load\provider_register();
    }
    return $GLOBALS["provider"]->make_mothod_static($class_name,$method,$params);
}
function is_cli()
{
    return preg_match("/cli/i", php_sapi_name()) ? true : false;
}
function index_path(){
    return \system\config\config::index_path();
}
