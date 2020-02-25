<?php
/**
 * Created by awesome.
 * Date: 2019-04-10 10:34:30
 */
namespace controller\index;
use controller\code\code_controller;
use controller\controller;
use db\db;
use db\factory\migration\migration;
use db\factory\migration\migration_list\migration_comment_list;
use db\factory\soft_db;
use db\model\comment_list\comment_list;
use db\model\model;
use db\model\user\user;
use load\provider_register;
use request\request;
use SuperClosure\Serializer;
use system\cache\cache;
use system\config\config;
use system\config\timed_task_config;
use system\file;
use system\mail;
use task\queue\queue;
use template\compile;

class index_controller extends controller
{
    public function index()
    {
//        $db=new db();
////        return $db->show_columns("user");
//          return soft_db::table("test11")
//              ->integer("id","5","not null",true,true)
//              ->text("vlog")
//              ->string("name","225","not null")
//              ->decimal("money","10","2")
//              ->timestamp("updated_at")
//              ->datetime("created_at")
//              ->create();
//        make("mail")->send_email("1293777844@qq.com","test","test")
//        return soft_db::table("comment_list")
//            ->join("user","name","user_id")
//            ->select("user_id","comment_content","head_img")
//            ->where("name","赵李杰","!=")
//            ->get();
//        $db=new db();
//        return $db->query(false,["table_name"=>["comment_list"=>["user_id","comment_content"],"user"=>["head_img"]],"link"=>["comment_list"=>"user_id","user"=>"name"]]);
//        soft_db::table("user")
//            ->where("name","赵李杰1")
//            ->where("email","1293777844@qq.com")
//            ->set("sex","man")
//            ->set("name","赵李杰")
//            ->update();
//        return soft_db::table("user")
//            ->all()
//            ->where("name","赵李杰")
//            ->get();
//
//        $user=new model();
//        return $user->id;
//
//        soft_db::table("user111")
//            ->string("name",20)
//            ->integer("user_id",20,true,true)
//            ->foreign_key("user_id","user11","id")
//            ->create();
//        $user=new model();
//        $user=$user->get();
//        $user->sex="man";
//        $user->update();
//        return $this->sex;
//        return microtime(true)-$GLOBALS["time"];
//        $model=new user();
//        $model->where("name","赵李杰")->get()->comment_list->comment_content="123";
//        var_dump($model->comment_list->time);
//        return microtime(true)-$GLOBALS["time"];
//        $redis=new \Redis();
//        $redis->connect("127.0.0.1", 6379);
//        while ($redis->lLen("emailfail")>0){
//            $data=unserialize($redis->lPop("emailfail"));
//            echo $data["fail"];
//            echo "</br>";
//        }
//        return;
//        return $redis->lLen("emailfail");
//        $redis->del("emailfail");
//        $queue=new queue();
//        $queue->push("email",["title"=>"欢迎注册","template"=>"login","user"=>"1293777844@qq.com","code"=>"sada"],"email");
//        return "ok";
//        $user1=new user();
//        $user1->where("name","赵李杰")->get();
//        $user1->sex="women";
//        $user1->update();
//        $user=new user();
//        $user->get();
//        $complie=new compile();
//        return $complie->view("test",$user->all());
//        $this->cache()->set_cache("sada","asd",10);
//        foreach ($this->cache()->get_all() as $value){
//            $value = json_decode($value, true);
//            if($value["expire"]!="forever") {
//                echo $value["key"] . date('Y-m-d H:i:s', $value["expire"]) . "<br>";
//            }
//            else{
//                echo $value["key"] .$value["expire"]. "<br>";
//            }
//        }
//    }
//        $user=new user();
//        return $user->get()->all();
//        $request=$this->request();
//        queue::asyn(function () use ($request){
//            echo "test";
//            $user=new user();
//            $user->where("name", $request->get("name"))->get();
//            $mail=new mail();
//            $mail->send_email($user->email,"test","test");
//        });
//        return microtime(true)-$GLOBALS["time"];
//        $compile=new compile();
//        $user=new user();
//        return $compile->view("test3",["user"=>$user->get()->all(),"head_img"=>"http://39.108.236.127/image/logo.png","name"=>"test"]);
//        $user=new user();
//        $user->where("name","赵李杰")->get();
//        $mail=new mail();
//        $mail->send_email($user->email,"test","test");
        $this->cache()->delete_key(timed_task_config::timed_task_schedule()[0]);
        //return $user->email;
        return code_controller::code(10);
    }
}