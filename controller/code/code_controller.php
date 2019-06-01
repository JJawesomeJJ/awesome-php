<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/18 0018
 * Time: 下午 7:03
 */
namespace controller\code;
use controller\auth\auth_controller;
use controller\controller;
use db\db;
use Grafika\Gd\Editor;
use Grafika\Grafika;
use system\Exception;
use system\file;
use system\template;
use system\vertify_code;
use task\add_task;
use template\compile;

class code_controller extends controller
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
        require_once @"/var/www/html/php/extend/vendor/kosinix/grafika/src/autoloader.php";
        try {
            $editor = new Editor();
            $crop_width = 100;
            $crop_height = 100;
            $editor = Grafika::createEditor();
            $file = new file();
            $file_list = $file->file_walk("/var/www/html/image/code_img/");
            $image_src = $file_list[array_rand($file_list)];
            $editor->open($image, $image_src);
            $width = $image->getWidth();
            $height = $image->getHeight();
            $start_x = mt_rand(0, $width - $crop_width);
            $start_y = mt_rand(0, $height - $crop_height - 20);
            $editor->crop($image, $crop_width, $crop_height, 'top-left', $start_x, $start_y);
            $filter = Grafika::createFilter('Blur', 80);
            $path = "/var/www/html/image/code_drop/" . time()."_".self::code(6)."_". ".jpg";
            setcookie("vertify_code_drop", json_encode(["src" => str_replace("/var/www/html/", "http://" . $_SERVER['HTTP_HOST'] . "/", $path), "height" => $start_y]), time()+3600,"/",$_SERVER['HTTP_HOST'],false,false);
            $editor->save($image, $path);
            $editor->apply($image, $filter);
            $editor->open($image1, $image_src);
            $editor->crop($image1, $width, $height - 20, 'top-left');
            $editor->blend($image1, $image, 'normal', 0.9, 'top-left', $start_x, $start_y);
            $editor->resizeFit($image1, 300, 300);
            if(!isset($_SESSION)){
                session_start();
            }
            $_SESSION["image_x"]=$start_x;
            header('Content-type: image/png'); // Tell the browser we're sending a png image
            $image1->blob('PNG');
        }
        catch (\Throwable $throwable){
            return $throwable->getMessage();
        }
    }
    public function slide_code(){
        $complie=new compile();
        return $complie->view("component/vertify/slide_vertify");
    }
    public function vertify_slide(){
        $rules=[
            "x"=>"requred|min:0|max:440"
        ];
        $this->request()->verifacation($rules);
        if(!isset($_SESSION)){
            session_start();
        }
        if(abs($this->request()->get("x")-$_SESSION["image_x"])<8){
            $_SESSION["pass"]="ok";
            unset($_SESSION["image_x"]);
            return ["code"=>200,"message"=>"ok"];
        }
        else{
            return ["code"=>403,"message"=>"fail_pass_vertify"];
        }

    }
}