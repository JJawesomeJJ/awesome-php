<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/16 0016
 * Time: 下午 2:38
 */
namespace request;
use db\db;
use system\config\config;
use system\Exception;
use system\upload_file;

class request
{
    public $user_input='*';
    protected $server;
    protected static $current_url;
    protected static $request_url;
    protected static $current_url_no_params;
    protected static $current_url_params;
    public function __construct()
    {
        $this->user_input=$this->all();
        unset($this->user_input['s']);
        $this->filter();
        $this->server=$_SERVER;
    }
    public function verifacation(array $rules){
        $arr=[];
        foreach ($rules as $key => $value) {
            $choose_list = explode("|", $value);
            foreach ($choose_list as $value) {
                $list = explode(":", $value);
                $rule_list = $list[0];
                if(isset($list[1])) {
                    $rule_list_var = $list[1];
                }
                switch ($rule_list) {
                    case "required":
                        $this->required($key);
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
                        $this->accept($this->get($key), explode(",", $rule_list_var));
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
                    case "is_number":
                        $this->is_number($this->get($key));
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
    //default params
    public function get($key,$default=false){
        if(isset($this->user_input[$key])){
            return $this->user_input[$key];
        }
        if($default!=false){
            return $default;
        }
        else{
           new Exception("400","request_key_not_exist_expect_$key");
        }
    }//get user input but if key not exist process will throw new Exception and process will shutdown
    public function get_ip_address(){
        return $_SERVER['REMOTE_ADDR'];
    }
    // get user ip address but this not only
    public function try_get($key){
        if(isset($this->user_input[$key])&&$this->user_input[$key]!=""){
            return $this->user_input[$key];
        }
        else{
           return false;
        }
    }
    //try to get user input if not exist will return false
    public function min($numer,$min){
        if(strlen($numer)>$min){
            return $numer;
        }
        else{
           new Exception("400","number_less_than_min_expect_$min");
        }
    }
    public function get_server($key){
        if(isset($this->server[$key])){
            return $this->server[$key];
        }
        return false;
    }
    public function get_many(array $key_list){
        $return_arr=[];
        foreach ($key_list as $key){
            $return_arr[$key]=$this->get($key);
        }
        return $return_arr;
    }
    public function is_number($var){
        if(is_numeric($var)){
            return $var;
        }else{
            new  Exception("400","variable_is_not_a_number");
        }
    }
    //vertify user input set min value
    public function max($numer,$max){
        if(strlen($numer)<(int)(int)$max){
            return $numer;
        }
        else{
           new Exception("400","number_more_than_max_except_$max");
        }
    }
    //set max input
    public function required($variable){
        if(!isset($this->user_input[$variable])){
            new Exception("400","variable_not_exist_expect_$variable");
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
        if(count($result)>0){
           new Exception("400","column_not_only_$column");
        }
        else{
            return true;
        }
    }//get data whether unique data in database
    public function equal($numer,$standard){
        if(strlen($numer)==(int)(int)$standard){
            return $numer;
        }
        else{
           new Exception("400","variable_length_not_equal_standard_expect_$standard");
        }
    }//vertify the number is equal another number
    public function accept($variable,array $arr){
        if(in_array($variable,$arr)){
            return $variable;
        }
        else{
            $message=json_encode($arr);
            new Exception("400","variable_accept_$message");
        }
    }//user input must be in we set
    public function email($str){
        if( filter_var($str, FILTER_VALIDATE_EMAIL) )
        {
            return $str;
        }
        else{
           new Exception("400","variable_is_not_a_email");
        }
    }
    //user input is a email
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
        if($this->user_input!='*'){
            return $this->user_input;
        }
        if(isset($_SERVER["CONTENT_TYPE"])) {
            if ($_SERVER["CONTENT_TYPE"] == "application/json;charset=UTF-8") {
                $this->user_input=json_decode(file_get_contents('php://input'), true);
                return $this->user_input;
            }
        }
        if($_SERVER['REQUEST_METHOD']=="GET"){
            $this->user_input=array_filter($_GET);
        }
        if($_SERVER['REQUEST_METHOD']=="POST"){
            $this->user_input=$_POST;
        }
        return $this->user_input;
    }
    public function get_file($name){
        return upload_file::upload_file($name);
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
        if(!is_null(self::$request_url)){
            return self::$request_url;
        }
        if(isset($_SERVER['PATH_INFO'])&&!empty($_SERVER['PATH_INFO'])){
            self::$request_url=str_replace('//','/',$_SERVER['PATH_INFO']);
        }
        $url=explode('index.php',$_SERVER['REQUEST_URI']);
        if(isset($url[1])){
            self::$request_url=str_replace('//','/',explode('?',$url[1])[0]);
        }
        else {
            self::$request_url = str_replace('//', '/', explode('?', $url[0])[0]);
        }
        if(config::url_html_suffix()!=''){
            self::$request_url=str_replace('.'.config::url_html_suffix(),'',self::$request_url);
        }
        return self::$request_url;
        //get_current_page_url if in index.php return false;
    }
    public function only(array $params){
        $arr=[];
        foreach ($params as $key){
            $arr[$key]=$this->get($key);
        }
        return $arr;
    }
    public function get_cookies($key){
        return $_COOKIE[$key];
    }
    public function get_cookies_all(){
        if(!isset($_COOKIE)){
            return null;
        }
        return $_COOKIE;
    }
    public function get_full_url($with_params=true){
        if($with_params) {
            if(self::$current_url_params==null) {
                self::$current_url_params=config::http_prefix() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                if(config::url_html_suffix()){
                    self::$current_url_params=str_replace('.'.config::url_html_suffix(),'',self::$current_url_params);
                }
            }
            return self::$current_url_params;
        }
        if(self::$current_url_no_params==null) {
            self::$current_url_no_params=config::http_prefix(). $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'])[0];
            if(config::url_html_suffix()){
                self::$current_url_no_params=str_replace('.'.config::url_html_suffix(),'',self::$current_url_no_params);
            }
        }
        return self::$current_url_no_params;
    }
    protected function filter()
    {
        $black_list=[">","<","<SCRIPT>", "\\", "</SCRIPT>", "<script>",'script', "</script>", "select", "select", "join", "join", "union", "union", "where", "where", "insert", "insert", "delete", "delete", "update", "update", "like", "like", "drop", "drop", "create", "create", "modify", "modify", "rename", "rename", "alter", "alter", "cas", "cast", "&", "&", ">", ">", "<", "<", " ", " ", "    ", "&", "'", "<br />", "''", "'", "css", "'", "CSS", "'"];
        foreach ($this->user_input as $input_value)
        {
            foreach($black_list as $black_list_value)
            {
                if(strpos(urldecode($input_value),$black_list_value)!==false)
                {
                    new Exception("403","danger_input_".$input_value.'_'.$black_list_value);
                }
            }
        }
    }
    public function get_http_host(){
        return 'http://'.$_SERVER['HTTP_HOST'];
    }
}