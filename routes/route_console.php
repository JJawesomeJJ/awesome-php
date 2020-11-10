<?php
namespace routes;
use app\console\demo\controllers\testController;
use db\model\user\user;
use request\request;
use system\mail;

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
routes::group(function (){

},[],"test/");