<?php
require_once ("../admin/class/database.php");
class token
{
    private $redis;
    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect("127.0.0.1", 6379);
    }

    public function set_token($user)
    {
        $emial="";
        $database=new database();
        $con=$database->login_database("register");
        $sql="select email from user where name='$user'";
        $result=$con->query($sql);
        while($row=mysqli_fetch_array($result)){
            $email=$row["email"];
        }
        if($email==""){
            return false;
        }
        $time=(string)time();
        $token_id = md5((base64_encode($user+substr($time,5,7))));
        $user_data = array(
            "name" => $user,
            "time" => time(),//时间戳
            "expire" => 604800,//设置超时
            "email" => $email,
        );
        $this->redis->hSet("token_list", $token_id, json_encode($user_data));
        setcookie("user_token",$token_id,time()+604800,"/",$_SERVER['HTTP_HOST'],false,true);
        return true;
    }

    public function check_token()
    {
        session_start();
        $token_id=$_COOKIE["user_token"];
        echo $this->redis->hGet("token_list",md5(base64_encode('赵李杰')));
        if ($this->redis->exists("token_list", $token_id) == true) {
            $user_info = $this->redis->hGet("token_list", $token_id);
            $user_info = get_object_vars(json_decode($user_info));
            if ((int)$user_info['time'] + (int)$user_info['expire'] > (int)time()) {
                $user_info['time'] = time();
                $_SESSION['name']=$user_info['name'];
                $_SESSION['email']=$user_info['email'];
                return true;
            } else {
                if($this->delete_token($token_id));
                {
                    return ["code"=>"403",'message'=>'timeout'];
                }
            }
        } else {
            $arr = [
                'code' => '403',
                'message' => "forbidden"
            ];
            return $arr;
        }
    }

    public function delete_token($token_id)
    {
        try {
            $this->redis->hDel("token_list", $token_id);
            setcookie("user_token",$token_id,time()-3600,"/",$_SERVER['HTTP_HOST'],false,true);
            return true;
        } catch (Exception $E) {
            return false;
        }
    }
}