<?php
$url=$_GET["url"];
require ("../admin/class/database.php");
$database=new database();
$con=$database->login_database("register");
$sql="SELECT * FROM `comment_list` WHERE url_id='$url'";
$result = mysqli_query($con, $sql);
$time=0;
$row=mysqli_fetch_array($result);
$arr=array();
$arr1=array(
    "comment"=>$row["comment_content"],
    "user_id"=>$row["user_id"]
);
array_push($arr,$arr1);
while($row=mysqli_fetch_array($result))
{
    $arr1=array(
        "comment"=>$row["comment_content"],
        "user_id"=>$row["user_id"]
    );
    array_push($arr,$arr1);
}
$call = sprintf("callback(%s)", json_encode($arr));
echo $call;
?>