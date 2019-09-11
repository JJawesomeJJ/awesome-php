<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/26 0026
 * Time: 下午 2:10
 */

namespace system;


use db\model\model_auto\model_auto;
use db\model\user\user;
use request\request;
use system\cache\cache;
use system\config\config;

class common
{
    public static function is_timestamp($timestamp) {
        if(strtotime(date('m-d-Y H:i:s',$timestamp)) === $timestamp) {
            return true;
        } else {
            return false;
        }
    }
    public static function get_array_value(array $keys,array $arr,$without_of_key=false){
        $return_arr=[];
        foreach ($keys as $key){
            if(!array_key_exists($key,$arr))
            {
                new Exception("400","array_key_not_exist_$key");
            }
            $return_arr[$key]=$arr[$key];
        }
        if($without_of_key){
            return array_values($return_arr);
        }
        return $return_arr;
    }
    public static function rand($num){
        $code_list="abcdefghijklmnopqrstuvwxyz123456789";
        if(!is_numeric($num)){
            new Exception("500","call_fun_error_num_should_be_a_number");
        }
        $code="";
        for ($i=0;$i<$num;$i++){
            $code.=substr($code_list,mt_rand(0,strlen($code_list)-1),1);
        }
        return $code;
    }
    public static function remember_me($id){
        $token=md5(microtime(true).self::rand(4));
        $cache=make("cache");
        $cache->set_cache($id,$token,604800);
        cookie::set("remember_me",json_encode(["token"=>$token,"id"=>$id]),604800);
    }
    public static function is_remember(){
        $cache=make("cache");
        if(!($user_info=cookie::get("remember_me"))){
            new Exception("600","user_certificate_timeout");
        }
        else{
            $user_info=json_decode($user_info,true);
            if($cache->get_cache($user_info["id"])!=$user_info["token"]){
                new Exception("600","user_certificate_timeout");
            }
            else{
                $user=new user();
                $user->where("id",$user_info["id"])->get()->all();
                session::set("name",$user->name);
                session::set("user",$user->name);
                session::set("email",$user->email);
                session::set("id",$user_info["id"]);
            }
        }
        return true;
    }
    public static function forget(){
        session::forget("name");
        session::forget("email");
        session::forget("id");
        if(cookie::get("remember_me")!=false) {
            $id = json_decode(cookie::get("remember_me"), true)["id"];
        }
        cookie::forget("remember_me");
        cookie::forget("csrf_token");
        $cache=new cache();
        $cache->delete_key($id);
    }
    public static function get_diff_time($start_at,$end_at,$is_string=true){
        $timediff = abs($end_at - $start_at);
        $days = intval( $timediff / 86400 );
        $remain = $timediff % 86400;
        $hours = intval( $remain / 3600 );
        $remain = $remain % 3600;
        $mins = intval( $remain / 60 );
        $secs = $remain % 60;
        if($is_string){
            $return_string="";
            if($days!=0){
                $return_string.=$days."天 ";
            }
            if($hours!=0){
                $return_string.=$hours."小时 ";
            }
            if($mins!=0){
                $return_string.=$mins."分 ";
            }
            if($secs!=0){
                $return_string.=$secs."秒";
            }
            return $return_string;
        }
        $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs );
    }
    public static function is_1_array(array $arr){
        if (count($arr) == count($arr, 1)) {
            return true;
        } else {
            return false;
        }
    }
    public static function array_group_by_key(array $arrs,$key,$unset_key=true){
        if(self::is_1_array($arrs)){
            return $arrs;
        }
        $return_arr=[];
        foreach ($arrs as $arr){
            if(!isset($arr[$key])){
                continue;
            }
            $name=$arr[$key];
            if(!isset($return_arr[$name])){
                $return_arr[$name]=[];
            }
            if($unset_key){
                unset($arr[$key]);
            }
            $return_arr[$name][]=$arr;
        }
        return $return_arr;
    }
    public static function str_replace_limit($search, $replace, $subject, $limit=-1)
    {
        if (is_array($search)) {
            foreach ($search as $k => $v) {
                $search[$k] = '`' . preg_quote($search[$k], '`') . '`';
            }
        } else {
            $search = '`' . preg_quote($search, '`') . '`';
        }
        return preg_replace($search, $replace, $subject, $limit);
    }
    public static function http_url_build(array $set_params){
        $request=make('request');
        $params=$request->all();
        foreach ($set_params as $key=>$value){
            $params[$key]=$value;
        }
        $url=$request->get_full_url(false)."?".http_build_query($params);
        if(config::url_html_suffix()){
            $url=$url."&.".config::url_html_suffix();
        }
        return $url;
    }
    public static function get_pager_list(){

    }
}