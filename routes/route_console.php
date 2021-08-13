<?php
namespace routes;
use app\console\book\BookController;
use app\console\demo\controllers\testController;
use app\console\tool\controllers\modelController;
use db\factory\soft_db;
use db\model\model_auto\model_auto;
use db\model\user\user;
use extend\awesome\awesome_driver_rabbitmq;
use extend\awesome\awesome_echo_tool;
use request\request;
use system\cache\cache;
use system\common;
use system\encrypt;
use system\mail;
use task\TimeTask\command\command;

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
routes::cli("book1586377",BookController::class."@index")->tick("2021-04-30",60*2);
//routes::cli("loadTask",function (mail $mail){
//    $mail->send_email("1293777844@qq.com","test","test");
//})->tick("2021-05-01 00:00:00",60);
routes::cli("decrypt",function (){
    echo "load".PHP_EOL;
    echo encrypt::ras_encrypt_private("OFnUhlXDjr8WTAtqH0EZc65YemCGRzB@");
});
routes::cli("version",function () {
    return version_compare("1.2021.1030.617", "1.2021.1025.524")===1;
});
routes::cli("faker",function (){
    $model=model_auto::model("ent_client_hard_disk_capacity_warning");
    $data=[];
    for ($i=0;$i<1000000;$i++){
        $data[]=[
            'cid'=>rand(1,300),
            'type'=>rand(0,1)
        ];
        if (count($data)>1500){
            echo $i.PHP_EOL;
            $model->create($data);
            $data=[];
        }
    }
    return $model->count();
});
routes::cli("cache",function (awesome_echo_tool $tool){
   return $tool->get_online_users();
});
routes::cli('fixedData',function (){
    $warningModel=model_auto::model('ent_client_hard_disk_capacity_warning');
    $model=clone $warningModel;
    $cids=array_column($warningModel->select(['cid'])->all(),'cid');
    $clientInfo=model_auto::model('ent_client')
        ->where_in('id',$cids)
        ->all();
    $clientInfo=common::array_group_by_key($clientInfo,'id');
    $deptInfo=soft_db::table("ent_client_group")
        ->select("cid","dept_id,name")
        ->where_in('cid',$cids)
        ->join("ent_dept",'id','dept_id')
        ->whereString("delete_date is null")
        ->get();
    $deptInfo=common::array_group_by_key($deptInfo,'cid');
    foreach ($cids as $cid){
        (clone $model)
            ->where('cid',$cid)
            ->update([
                'client_name'=>$clientInfo[$cid][0]['client_name'],
                'computer_name'=>$clientInfo[$cid][0]['computer_name'],
                'remark'=>$clientInfo[$cid][0]['remark'],
                'dept_id'=>empty($deptInfo[$cid])?0:$deptInfo[$cid][0]['dept_id'],
                'dept_name'=>empty($deptInfo[$cid])?'未分组':$deptInfo[$cid][0]['name'],
            ]);

    }
});
routes::cli('times',function (){
    return date('Y-m-t');
});
routes::cli('matchNum',function (){
   var_dump(round(99.99,0)==100);
});
routes::cli('test122',function (){
    return model_auto::model('ent_client_hard_disk_capacity_warning')->ReadMaster()->first_cache()
        ->count();
});
routes::cli("timetest",function (){
   return date('Y-m-d',strtotime("+15 day 2021-08-03"));
});
routes::cli("sql-test",function (){
    return soft_db::table('ent_user')
        ->select("name")
        ->or_where('user','赵李杰')
        ->get();
});
//routes::cli('jjawesome',function (){
//    echo "load".PHP_EOL;
//    sleep(10);
//})->tick("2021-07-02",5);

