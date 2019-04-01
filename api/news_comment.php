<?php
session_start();
require ("../admin/class/database.php");
$database=new database();
$con=$database->login_database("register");
$commnet=$_GET["comment"];
$url=$_GET["url"];
$flag=false;
if(isset($_SESSION['user'])==false)
{
    $name=urldecode($_COOKIE["name"]);
    $sql = "SELECT * FROM `user` WHERE name='$name' ";
    $result = mysqli_query($con, $sql);
    if(($row=mysqli_fetch_array($result))>0) {
        $pass=hash('SHA256', "19971998");
        if ($pass==$_COOKIE["sha"])
        {
            $_SESSION["user"] = $name;
            $flag = true;
        }
    }
    //$con->close();
    if($flag==false) {
        $arr = array(
            "code" => "403",
            "data" => "forbidden"
        );
        echo json_encode($arr);
        return;
    }
}
$name=$_SESSION['user'];
$sql="SELECT * FROM `comment_list` WHERE url_id='$url'";
$result = mysqli_query($con, $sql);
$time=0;
$time1=date('Y-m-d');
if(($row=mysqli_fetch_array($result))<1)
{
    $sql1="INSERT INTO `comment_list`(`url_id`, `id`, `user_id`, `comment_content`, `time`) VALUES ('$url',$time,'$name','$commnet','$time1')";
    if($con->query($sql1)==TRUE) {
        $arr=array(
            "code"=>"200"
        );
        $call = sprintf("callback(%s)", json_encode($arr));
        echo $call;
    }
}
else {
    $time = $time + 1;
    while ($row = mysqli_fetch_array($result)) {
        $time = $time + 1;
    }
    $arr = array(
        "code" => "200"
    );

    $sql1="INSERT INTO `comment_list`(`url_id`, `id`, `user_id`, `comment_content`, `time`) VALUES ('$url',$time,'$name','$commnet','$time1')";
    if($con->query($sql1)==TRUE) {
        $arr=array(
            "code"=>"200"
        );
        $call = sprintf("callback(%s)", json_encode($arr));
        echo $call;
    }

}

?>