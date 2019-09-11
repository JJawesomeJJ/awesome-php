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
use load\auto_load;
use request\request;
use system\config\config;
use system\Exception;
use system\file;
use system\session;
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
        session::set('admin_email_code',$code);
        $content=view('login',['title'=>'欢迎登陆停车帮后端','code'=>$code]);
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
            session::set($session_filed,$code);
        }
        return $code;
    }
    public function img_cut_square(){
        require_once @config::env_path()."/extend/vendor/kosinix/grafika/src/autoloader.php";
        try {
            $editor = new Editor();
            $crop_width = 100;
            $crop_height = 100;
            $editor = Grafika::createEditor();
            $file = new file();
            $file_list = $file->file_walk(config::www_path()."image/code_img/");
            $image_src = $file_list[array_rand($file_list)];
            $editor->open($image, $image_src);
            $width = $image->getWidth();
            $height = $image->getHeight();
            $start_x = mt_rand(0, $width - $crop_width);
            $start_y = mt_rand(0, $height - $crop_height - 20);
            $editor->crop($image, $crop_width, $crop_height, 'top-left', $start_x, $start_y);
            $filter = Grafika::createFilter('Blur', 80);
            $name=time()."_".self::code(6)."_". ".jpg";
            $path = config::env_path().'/public/'."image/code_drop/" .$name;
            setcookie("vertify_code_drop", json_encode(["src" => config::project_path().'/image/code_drop/'.$name, "height" => $start_y]), time()+3600,"/",$_SERVER['HTTP_HOST'],false,false);
            $editor->save($image, $path);
            $editor->apply($image, $filter);
            $editor->open($image1, $image_src);
            $editor->crop($image1, $width, $height - 20, 'top-left');
            $editor->blend($image1, $image, 'normal', 0.9, 'top-left', $start_x, $start_y);
            $editor->resizeFit($image1, 300, 300);
            session::set("image_x",$start_x);
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
        if(abs($this->request()->get("x")-session::get("image_x"))<8){
            session::set("pass","ok");
            session::forget("image_x");
            return ["code"=>200,"message"=>"ok"];
        }
        else{
            return ["code"=>403,"message"=>"fail_pass_vertify"];
        }

    }
    public function code_(){
        header ('Content-Type: image/png');
        $image=imagecreatetruecolor(100, 30);
//背景颜色为白色
        $color=imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 20, 20, $color);
        $code='';
        for($i=0;$i<4;$i++){
            $fontSize=8;
            $x=rand(5,10)+$i*100/4;
            $y=rand(5, 15);
            $data='abcdefghijklmnopqrstuvwxyz123456789';
            $string=substr($data,rand(0, strlen($data)),1);
            $code.=$string;
            $color=imagecolorallocate($image,rand(0,120), rand(0,120), rand(0,120));
            imagestring($image, $fontSize, $x, $y, $string, $color);
        }
        session::set('code',$code);//存储在session里
        for($i=0;$i<200;$i++){
            $pointColor=imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
            imagesetpixel($image, rand(0, 100), rand(0, 30), $pointColor);
        }
        for($i=0;$i<2;$i++){
            $linePoint=imagecolorallocate($image, rand(150, 255), rand(150, 255), rand(150, 255));
            imageline($image, rand(10, 50), rand(10, 20), rand(80,90), rand(15, 25), $linePoint);
        }
        imagepng($image);
        imagedestroy($image);
    }
    public function qrcode(request $request){
        auto_load::load('qrcode/phpqrcode');
        \QRcode::png($request->get('url',$_SERVER['HTTP_HOST']));
    }
}