<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/15 0015
 * Time: 下午 3:52
 */
require ("../admin/class/database.php");
$arr=[];
$database2 = new database();
$con = $database2->login_database("register");
$sql="select * from survey";
$result=$con->query($sql);
$str="";
while($row=mysqli_fetch_array($result))
{
    if($row['result']==""){
        echo "fsdf";
    }
}