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
use Grafika\Gd\Editor;
use request\request;
use system\Exception;
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
        $request=$this->request()->verifacation($rules);
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
    public static function code($num,$session_filed=false){
        $code_list="abcdefghijklmnopqrstuvwxyz123456789";
        if(!is_numeric($num)){
            new Exception("500","call_fun_error_num_should_be_a_number");
        }
        $code="";
        for ($i=0;$i<$num;$i++){
            $code.=substr($code_list,mt_rand(0,strlen($code_list)-1),1);
        }
        if($session_filed){
            if(!isset($_SESSION)){
                session_start();
            }
            $_SESSION[$session_filed]=$code;
        }
        return $code;
    }
    public function img_cut_square(){
        require_once "../../extend/vendor/kosinix/grafika/src/autoloader.php";
        $editor=new Editor();
    }
}