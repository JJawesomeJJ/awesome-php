<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/18 0018
 * Time: 下午 7:03
 */
namespace controller\code;
use controller\auth\auth_controller;
use db\db;
use request\request;
use system\file;
use system\template;
use system\vertify_code;
use task\add_task;

class code_controller
{
    public function email_code($user_email,$content,$subject){
        $add_task=new add_task();
        $add_task->add('task_list','send_email',['user_email'=>$user_email,'data'=>$content,'subject'=>$subject]);
    }
    public function map_admin_email(){
        session_start();
        $rules=[
            "name"=>"required:get",
        ];
        $request=new request($rules);
        $name=$request->get('name');
        $db=new db();
        $result=$db->query('admin_user',['email'],"name='$name'");
        if(count($result)==0)
        {
            return ['code'=>'404','admin_user_unsign'];
        }
        $code=vertify_code::random_code(4);
        $_SESSION['admin_email_code']=$code;
        $content=template::template('login',['title'=>'欢迎登陆停车帮后端','code'=>$code]);
        $this->email_code($result['email'],$content,'停车帮欢迎你');
    }
}