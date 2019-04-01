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
    protected $rules=[
        ];
    private $arr;
    public function __construct(array $rules)
    {
        $this->rules=$rules;
        try {
            $this->arr = self::rules();
        }
        catch (\Exception $exception)
        {
            echo strlen($exception);
        }
    }
    public function rules(){
        $arr=[];
        foreach ($this->rules as $key => $value) {
            $choose_list = explode("|", $value);
            foreach ($choose_list as $value) {
                $list = explode(":", $value);
                $rule_list = $list[0];
                $rule_list_var = $list[1];
                switch ($rule_list) {
                    case "required":
                        $arr[$key] = $this->required($key, $rule_list_var);
                        break;
                    case "min":
                        $this->min($arr[$key], $rule_list_var);
                        break;
                    case "max":
                        $this->max($arr[$key], $rule_list_var);
                        break;
                    case "unique":
                        $this->unique($key, $arr[$key], $rule_list_var);
                        break;
                    case "equal":
                        $this->equal($arr[$key], $rule_list_var);
                        break;
                    case "accept":
                        $this->accepet($arr[$key], explode(",", $rule_list_var));
                        break;
                    case "email":
                        $this->email($arr[$key]);
                        break;
                    case "confirm":
                        $this->confirm($arr[$key], $rule_list_var);
                        break;
                    case "is_tele":
                        $this->is_tele($arr[$key]);
                        break;
                    default:
                        break;
                }
            }
        }
        return $arr;
    }
    public function get($key){
        if($_REQUEST[$key]!=""){
            return $_REQUEST[$key];
        }
        else{
           new Exception("400","request_key_not_exist_expect_$key");
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
    public function unique($column,$column_value,$table_name){
        $db=new db();
        $result=$db->query($table_name,[$column],"$column='$column_value'");
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
    public function all(){
        if($_SERVER['REQUEST_METHOD']=='GET')
        {
            $parmas_list=[];
            $url=$_SERVER['REQUEST_URI'];
            $url_data=explode("?",$url);
            $get_params=explode("&",$url_data[1]);
            foreach ($get_params as $value)
            {
                $parms_key_value=explode('=',$value);
                $parmas_list[$parms_key_value[0]]=$parms_key_value[1];
            }
            return $parmas_list;
        }
        if($_SERVER['REQUEST_METHOD']=='POST')
        {
            $parms=file_get_contents("php://input");
            $get_params=explode("&",$parms);
            foreach ($get_params as $value)
            {
                $parms_key_value=explode('=',$value);
                $parmas_list[$parms_key_value[0]]=$parms_key_value[1];
            }
            return $parmas_list;
        }
    }
}