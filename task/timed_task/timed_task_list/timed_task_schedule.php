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
    $picture_list=$file->file_walk(\system\config\config::env_path()."public/image/code_drop");
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
//\task\queue\timed_task::add_closure_timed_task("swoole_dev",function (){
//    $is_dev=false;
//    $cache=new \system\cache\cache();
//    $file=new \system\file();
//    $except=\system\config\config::swoole_dev()['except'];
//    $scan_path=\system\config\config::swoole_dev()['scan'];
//    foreach ($scan_path as $path){
//        $file_list=$file->file_walk($path,$except);
//        foreach ($file_list as $file_name){
//            if(($md=$cache->get_non_exist_set($file_name,function () use ($file_name){
//                    echo "文件未加载跳过".PHP_EOL;
//                    return md5_file($file_name);
//                    },'forever',false,false))===true){
//                continue;
//            }
//            if($md!=($md5=md5_file($file_name))){
//                $cache->set_cache($file_name,$md5,'forever');
//                echo "不同应该热更新".PHP_EOL;
//                $is_dev=true;
//            }
//        }
//    }
//    if($is_dev){
//        $http=new \system\http();
//        $http->post(\system\config\config::server()["host_ip"].":9555/123",["password"=>19971998]);
//        echo "load";
//        $is_dev=false;
//    }
//},"11:27",60);
////\task\queue\timed_task::add_closure_timed_task("dev",,"15:00",86400);
////\task\queue\timed_task::add_closure_timed_task("send_email_2_3",function (){
////    $user=new \db\model\user\user();
////    $user->where("name","赵李杰")->get();
////    $mail=new \system\mail();
////    $mail->send_email($user->email,"test",'test');
////},"16:51",200,5);
//\task\queue\timed_task::add_closure_timed_task('check_server',function (){
//    $http=new \system\http();
//    $response=$http->get('https://www.tmxiaoer.com/');
//    if(strpos($response,'系统错误')!==false){
//        $email=new \system\mail();
//        $email->send_email('1293777844@qq.com',$response,'server_error_service_monitor_jj_awesome');
//    }
//},"11:15",60*10);
\task\queue\timed_task::add_closure_timed_task('refresh_news_likes',function (){
    $redis=\system\class_define::redis();
    $data=$redis->hGetAll("news_cache_record");
    foreach ($data as $key=>$value){
        if($value+60*60*24<time()){
            $commnet_list=$redis->hGetAll($key);
            foreach ($commnet_list as $comment){
                if(!isset($comment['form'])){//数据从未更新入库
                    $comment_object=new \db\model\comment_list\comment_list();
                    $comment_object->create($comment);
                }
            }
            $redis->hDel("news_cache_record",$key);
            $redis->del($key);
        }
    }
},"23:00",60*60*24);//24小时更新一次释放缓存
