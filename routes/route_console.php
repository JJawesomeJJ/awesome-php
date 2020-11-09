<?php
namespace routes;
use app\console\demo\controllers\testController;
use db\model\user\user;

routes::cli("cli",function (user $user){
    print_r($user->limit(1,1)->all());
});
routes::cli("test",'testController'."@index");