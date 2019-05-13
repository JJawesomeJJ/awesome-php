<?php
namespace system;
use system\Exception;
class token
{
    private $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", 6379);
    }

    public function set_token($user,$email,$store_name=false,$params=false)//参数三指向地址 参数四存储的内容
    {
        $time=(string)time();
        $token_store_name="token_list";
        $token_id = md5((base64_encode($user)));
        $remember_me=md5($user.time());
        $user_data = array(
            "remember_me"=>$remember_me,
            "name" => $user,
            "time" => time(),//时间戳
            "expire" => 604800,//设置超时
            "email" =>$email,
        );
        if($store_name!=false)
        {
            $token_store_name=$store_name;
            if($params!=false)
            {
                foreach ($params as $key=>$value)
                {
                    $user_data[$key]=$value;
                }
            }
        }
        $this->redis->hSet($token_store_name, $token_id, json_encode($user_data));
        setcookie("user_token",$token_id,time()+604800,"/",$_SERVER['HTTP_HOST'],false,true);
        setcookie("remember_me",$remember_me,time()+604800,"/",$_SERVER['HTTP_HOST'],false,true);
        return true;
    }
    public function check_token($store_name=false,$params_list=false)
    {
        if(!isset($_SESSION)){ session_start(); }
        $token_store_name="token_list";
        if($store_name!=false)
        {
            $token_store_name=$store_name;
        }
        if(!isset($_COOKIE["user_token"])||!isset($_COOKIE["remember_me"]))
        {
            new Exception("600","user_certificate_failure");
        }
        $token_id=$_COOKIE["user_token"];
        if ($token_id!=""&&$this->redis->exists($token_store_name) == true) {
            $user_info = $this->redis->hGet($token_store_name, $token_id);
            $user_info =json_decode($user_info,true);
            //var_dump($user_info);
            if ($user_info['remember_me']==$_COOKIE['remember_me']&&((int)$user_info['time'] + (int)$user_info['expire'])> (int)time()&&$user_info['remember_me']!="") {
                $user_info['time']=time();
                if($params_list!=false)
                {
                    foreach ($params_list as $key=>$value) {
                        $_SESSION[$key] = $user_info[$value];
                    }
                }
                $this->redis->hSet("token_list", $token_id, json_encode($user_info));
                if(func_num_args()==0) {
                    $_SESSION['user'] = $user_info['name'];
                    $_SESSION['email'] = $user_info['email'];
                }
                return true;
            } else {
                if($this->delete_token($token_id));
                {
                    new Exception("600","user_certificate_timeout");
                }
            }
        } else {
            new Exception("600","user_certificate_failure");
        }
    }
    public function delete_token($token_id,$store_name=false)
    {
        $token_store_name="token_list";
        if($store_name!=false)
        {
            $token_store_name=$store_name;
        }
        try {
            $this->redis->hDel($token_store_name, $token_id);
            setcookie("user_token",$token_id,time()-3600,"/",$_SERVER['HTTP_HOST'],false,true);
            setcookie("remember_me",$_COOKIE['remember_me'],time()-3600,"/",$_SERVER['HTTP_HOST'],false,true);
            return true;
        } catch (Exception $E) {
            return false;
        }
    }
}