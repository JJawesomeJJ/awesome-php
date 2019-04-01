<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/28 0028
 * Time: 上午 8:13
 */
//creaae by jjawesome 用于服务器是存在session；不存则创建；检测cookies是否合法；采用sha256加密；目前网络上有许多sha256的解密库；尽量使用复杂远的原密码；
require ("../admin/class/database.php");
class user_info
{
    public static function session_check(){
        session_start();
        $flag=false;
        if(isset($_SESSION['user'])==false)
        {

            $database=new database();
            $name=urldecode($_COOKIE['name']);
            $sql = "SELECT * FROM `user` WHERE name='$name' ";
            $con=$database->login_database("register");
            $result = mysqli_query($con, $sql);
            if(($row=mysqli_fetch_array($result))>0) {
                $pass=hash('SHA256', $row["password"]);
                if ($pass==$_COOKIE["sha"])
                {
                    $_SESSION["user"] = $name;
                    $flag = true;
                }
            }
            $con->close();
            if($flag==false) {
                $arr = array(
                    "code" => "403",
                    "data" => "forbidden"
                );
                echo json_encode($arr);
                return;
            }
        }
    }
}