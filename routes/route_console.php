<?php
namespace routes;
use app\console\book\BookController;
use app\console\demo\controllers\testController;
use app\console\tool\controllers\modelController;
use db\model\user\user;
use request\request;
use system\mail;

routes::cli("model/create/{table}",modelController::class."@create");
routes::cli("start",function (){
    echo microtime(true)-start_at;
});
routes::cli("cli",function (user $user){
    print_r($user->limit(1,1)->all());
});
routes::cli("test",'testController'."@index");
routes::cli("email",function (mail $mail){
    $mail->send_email("1293777844@qq.com","dasda","dasd");
});
routes::cli("echo/{name}",function (request $request){
    print_r($request->all());die();
    while (1)
    echo microtime(true).PHP_EOL;
});

routes::cli("company",function (t_company $company){
    $data=$company->select(["id","name","pid",'layer'])->all();
    function compile_menu(array $menu_list,int $pid=0,int $layer=2){
        $result=[];
        foreach ($menu_list as $item){
            if($item['pid']==$pid){//
                $children=compile_menu($menu_list,$item['id']);
                if(!empty($children)){
                    $item['children']=$children;
                }
                $result[]=$item;
            }
        }
        return $result;
    }
    $data=compile_menu($data,0);
    print_r($data);
});
routes::group(function (){

},[],"test/");
routes::cli("explode",function (){
    print_r(explode('文',"中文 符号"));
});
routes::cli("book",BookController::class."@index")->tick("2021-04-30",60*2);
//routes::cli("loadTask",function (mail $mail){
//    $mail->send_email("1293777844@qq.com","test","test");
//})->tick("2021-05-01 00:00:00",60*10);