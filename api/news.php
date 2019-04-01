<?php
require ("../admin/class/database.php");
$url=$_GET['url'];
$database=new database();
$con=$database->login_database("register");
$sql="SELECT * FROM `new_content` WHERE url_id='$url'";
$result = mysqli_query($con, $sql);
if(($row=mysqli_fetch_array($result))<1)
{
    require ('./news_content.php');
    $new_content=new news();
    $content=$new_content->news_content();
    $time=date('Y-m-d');
    $sql1="INSERT INTO `new_content`(`url_id`, `url_content`, `time`) VALUES ('$url','$content','$time')";
    if($con->query($sql1) == TRUE){
        $call = sprintf("callback(%s)", json_encode($content));
        echo $call;
    }
    else{
        $call = sprintf("callback(%s)", json_encode($content));
        echo $call;
    }
//$call = sprintf("callback(%s)", json_encode($content));
    //echo $content;
}
else{
    if($row["url_content"]==""&&$row["url_content"]==null){
        require ('./news_content.php');
        $new_content=new news();
        $content=$new_content->news_content();
        $call = sprintf("callback(%s)", json_encode($content));
        echo $call;
        return;
    }
    $call = sprintf("callback(%s)", json_encode($row["url_content"]));
    echo $call;
}
?>

