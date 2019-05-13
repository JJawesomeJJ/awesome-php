<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/16 0016
 * Time: 下午 2:38
 */
namespace request;
use db\db;
use system\Exception;

class request
{
    public $user_input;
    public function __construct($rules=false)
    {
        $this->user_input=$this->all();
    }
    public function verifacation(array $rules){
        $arr=[];
        foreach ($rules as $key => $value) {
            $choose_list = explode("|", $value);
            foreach ($choose_list as $value) {
                $list = explode(":", $value);
                $rule_list = $list[0];
                $rule_list_var = $list[1];
                switch ($rule_list) {
                    case "required":
                        $this->required($key, $rule_list_var);
                        break;
                    case "min":
                        $this->min($this->get($key), $rule_list_var);
                        break;
                    case "max":
                        $this->max($this->get($key), $rule_list_var);
                        break;
                    case "unique":
                        $this->unique($key, $this->get($key), $rule_list_var);
                        break;
                    case "equal":
                        $this->equal($this->get($key), $rule_list_var);
                        break;
                    case "accept":
                        $this->accepet($this->get($key), explode(",", $rule_list_var));
                        break;
                    case "email":
                        $this->email($this->get($key));
                        break;
                    case "confirm":
                        $this->confirm($this->get($key), $rule_list_var);
                        break;
                    case "is_tele":
                        $this->is_tele($this->get($key));
                        break;
                    case "regex":
                        $this->regex($this->get($key),$rule_list_var);
                        break;
                    default:
                        break;
                }
            }
        }
        return $this;
    }
    //use rules_array to to vertify_user_input
    //bug log when input rules_var include char like '\',':' some trouble may happen
    public function get($key){
        if(isset($this->user_input[$key])){
            return $this->user_input[$key];
        }
        else{
           new Exception("400","request_key_not_exist_expect_$key");
        }
    }
    public function get_ip_address(){
        return $_SERVER['REMOTE_ADDR'];
    }
    public function try_get($key){
        if(isset($this->user_input[$key])){
            return $this->user_input[$key];
        }
        else{
           return false;
        }
    }
    public function min($numer,$min){
        if(strlen($numer)>$min){
            return $numer;
        }
        else{
           new Exception("400","number_less_than_min_expect_$min");
        }
    }
    public function max($numer,$max){
        if(strlen($numer)<(int)(int)$max){
            return $numer;
        }
        else{
           new Exception("400","number_more_than_max_except_$max");
        }
    }
    public function required($variable,$request_method){
        if(strtolower($_SERVER['REQUEST_METHOD'])==strtolower($request_method)){
            if(isset($_REQUEST[$variable]))
            {
                return $_REQUEST[$variable];
            }
            else{
               new Exception("400","variable_not_exist_expect_$variable");
            }
        }else{
           new Exception("400","request_methoh_error_expect_$request_method");
        }
    }
    public function get_oringin($key){
        if(isset($_REQUEST[$key])){
            return $_REQUEST[$key];
        }
        else{
            new Exception("400","variable_not_exist_expect_$key");
        }
    }//get_oringin_user_input_without_of_middlware_handdle
    public function unique($column,$column_value,$table_name){
        $db=new db();
        $result=$db->query($table_name,[$column],"$column='$column_value'");
        if(count($result)==0){
            return true;
        }
        if(count($result[$column])>0){
           new Exception("400","column_not_only_$column");
        }
        else{
            return true;
        }
    }
    public function equal($numer,$standard){
        if(strlen($numer)==(int)(int)$standard){
            return $numer;
        }
        else{
           new Exception("400","variable_length_not_equal_standard_expect_$standard");
        }
    }
    public function accepet($variable,array $arr){
        if(in_array($variable,$arr)){
            return $variable;
        }
        else{
            $message=json_encode($arr);
           new Exception("400","variable_accept_$message");
        }
    }
    public function email($str){
        if( filter_var($str, FILTER_VALIDATE_EMAIL) )
        {
            return $str;
        }
        else{
           new Exception("400","variable_is_not_a_email");
        }
    }
    public function confirm($str,$key){
        if($this->get($key)==$str)
        {
            return $str;
        }
        else{
           new Exception("400","variable_fail_confirm");
        }
    }
    public function is_tele($tele){
        if(preg_match("/^1[34578]{1}\d{9}$/",$tele)){
            return $tele;
        }else{
           new Exception("400","expect_tele");
        }

    }
    private function regex($value,$regex_express){
        if(preg_match("/$regex_express/",$value)) {
            return $value;
        }
        else{
            new Exception("400","variable_fail_to_pass_regex");
        }

    }
    public function all(){
        if($_SERVER['REQUEST_METHOD']=="GET"){
            return $_GET;
        }
        if($_SERVER['REQUEST_METHOD']=="POST"){
            return $_POST;
        }
    }
    public function request_mothod(){
        if($this->try_get("_method")){
            return $this->get("_method");
        }
        return $_SERVER["REQUEST_METHOD"];
    }//get_request_mothod like get/post/put/delete
    public function get_referer_url(){
        return $_SERVER["HTTP_REFERER"];
    }//get_request_referer_url;
    public function get_url(){
        $url=explode('.php/',$_SERVER['PHP_SELF']);
        if(count($url)=='1') {
            return false;
        }
        else{
            return $url[1];
        }
        //get_current_page_url if in index.php return false;
    }
    public function add_callback($object,$method){
        $this->call_back[]=["object"=>$object,"method"=>$method];
    }
    public function only(array $params){
        $arr=[];
        foreach ($params as $key){
            $arr[$key]=$this->get($key);
        }
        return $arr;
    }
}