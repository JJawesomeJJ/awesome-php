<?php
require_once __DIR__."/../../../load/auto_load.php";
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/23 0023
 * Time: 下午 9:27
 * schedule description
 * this a timed_task schedule
 * you can write timed_task
 * task will run when you set time
 */
\task\queue\timed_task::add_closure_timed_task("send_email",function (){
    $user=new \db\model\user\user();
    $user->where("name","赵李杰")->get();
    $mail=new \system\mail();
    $mail->send_email($user->email,"test",'test');
},"24:00",86400);
\task\queue\timed_task::add_closure_timed_task("clear_expired_picture",function (){
    $file=new \system\file();
    $picture_list=$file->file_walk("/var/www/html/image/code_drop/");
    foreach ($picture_list as $name){
        $time=basename($name);
        $time=str_replace(".jpg","",$time);
        $time=preg_replace("/_(.*?)_/","",$time);
        if(is_numeric($time)&&strlen($time)==10){
            if(time()-$time>3600) {
                $file->delete_file($name);
            }
        }
    }
},"2:20",86400);
\task\queue\timed_task::add_closure_timed_task("send_email_2",function (){
    $user=new \db\model\user\user();
    $user->where("name","赵李杰")->get();
    $mail=new \system\mail();
    $mail->send_email($user->email,"test",'test');
},"15:00",86400);
//\task\queue\timed_task::add_closure_timed_task("send_email_2_3",function (){
//    $user=new \db\model\user\user();
//    $user->where("name","赵李杰")->get();
//    $mail=new \system\mail();
//    $mail->send_email($user->email,"test",'test');
//},"16:51",200,5);
//\task\queue\timed_task::add_closure_timed_task("send_email_2_3_test_1_2",function (){
//    $compile=new \template\compile();
////    $user=new \db\model\user\user();
//////    $user->where("name","赵李杰")->get();
//    $mail=new \system\mail();
//    $mail->send_email("844104772",$compile->view("tool/xiaoyi"),'生日快乐');
//},"8:00",1000,1);
