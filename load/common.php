<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/2 0002
 * Time: 下午 3:35
 */
//register_shutdown_function("on_app_stop");
//set_error_handler("on_app_stop", E_ALL | E_STRICT);
//set_exception_handler("on_app_stop");
function make($class_name)
{
    return \load\provider_register::provider()->make($class_name);
}
//function app(){
//    if(!isset($GLOBALS["app"])){
//        $app=new \load\app();
//        $GLOBALS["app"]=$app;
//        return $app;
//    }
//    else{
//        return $GLOBALS["app"];
//    }
//}
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
    return \load\provider_register::provider()->make_method($method,$class_name);
}
function make_method_static($class_name,$method,$params=[]){
    return \load\provider_register::provider()->make_mothod_static($class_name,$method,$params);
}
function is_cli()
{
    return \system\config\config::is_cli();
}
function index_path(){
    return \system\config\config::index_path();
}
function event($event_name){
    \system\kernel\event\event_system::SingleTon()->trigger($event_name);
}
function app(){
    return \load\provider_register::provider();
}
/**
 * @description 获取当前程序运行的时间
 * @return mixed
 */
function runtime(){
    return microtime(true)-start_at;
}
function is_1_array(array $arr){
    if (count($arr) == count($arr, 1)) {
        return true;
    } else {
        return false;
    }
}