<?php
require ("../admin/class/database.php");
$database=new database();
$con=$database->login_database("register");
$time=date('Y-m-d');
$sql="DELETE FROM `comment_list` WHERE time!='$time'" ;
$sql1="DELETE FROM `new_content` WHERE time!='$time'" ;
if($con->query($sql)==TRUE)
{
    echo "$sql";
}
if($con->query($sql1)==TRUE)
{
    echo "$sql1";
}
exit(0);
?>