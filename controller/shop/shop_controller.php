<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/14 0014
 * Time: 下午 10:57
 */
namespace controller\shop;
use controller\admin_user\admin_user_controller;
use controller\controller;
use db\db;
use db\model\shop\categories;
use db\model\model_auto\model_auto;
use db\model\shop\goods;
use db\model\user\user;
use load\provider_register;
use request\request;
use system\class_define;
use system\common;
use system\cookie;
use system\session;

class shop_controller extends controller
{
    public function construct()
    {
        parent::construct(); // TODO: Change the autogenerated stub
        if($this->request()->request_mothod()=='GET') {
            common::log_request();
        }
    }

    public function index(user $user,request $request,goods $goods,categories $categories){
        $user_info=admin_user_controller::permission(false);
        if(empty($user_info)){
            redirect(index_path()."shop/login");
        }
        $new_user_count=$user->where('created_at','>',date("Y-m-d H:i:s", strtotime("-1 year")))->count();
        $visitor=model_auto::model('request_user');
        $count=$visitor
            ->where_bettween('request_time',date('Y-m-d'),date('Y-m-d',strtotime('+1 day')))
            ->group_by('rqid')
            ->count(true,'ip');
        if(!is_numeric($count)){
            $count=count($count);
        }else{
            $count=1;
        }
        return view('shop/index',
            [
                'user_info'=>$user_info,
                'register_new'=>$new_user_count,
                'visitor'=>$count,
                'goods_num'=>$goods->count(),
                'categories_num_1'=>$categories->where('level',1)->count(true),
                'categories_num_2'=>$categories->where('level',2)->count(true),
                'request'=>$request->all(),
            ]);
    }
    public function login(request $request){
        if($request->request_mothod()=='GET'){
            $user_info=admin_user_controller::permission(false);
            if(!empty($user_info)){
                redirect(index_path()."shop/index");
            }
            return view('shop/login');
        }
    }
    public function get_request_info(){
        $redis=class_define::redis();
        $request_object=model_auto::model('request_user');
        $insert_info=[];
        while (($data=$redis->lPop('request_log'))){
            $insert_info[]=json_decode($data,true);
        }
        $request_object->create($insert_info);
        $time_start=date('Y-m-d')." 00:00:00";
        $time_end=date('Y-m-d',strtotime('+1 day'))." 00:00:00";
        $data=$request_object->where_bettween('request_time',$time_start,$time_end)->all();
        $request_count=common::array_group_by_key_time($data,'request_time','H',false,true);
        $hour=date('H');
        for ($i=1;$i<=$hour;$i++){
            if(!isset($request_count[$i])){
                $request_count[$i]=0;
            }
        }
        $data=[];
        $sort_key=array_keys($request_count);
        sort($sort_key);
        foreach ($sort_key as $item){
            $data[]=$request_count[$item];
        }
        return $data;
    }
    public function table(request $request){
        $user_info=admin_user_controller::permission(false);
        if(empty($user_info)){
            redirect(index_path()."shop/login");
        }
        $categories_model=new categories();
        if($request->try_get('name')){
            $categories_model->where_like('name',$request->get('name'));
        }
        else{
            $categories_model->where('level',1);
        }
        $data=$categories_model->page($request->get('page',1),10);
        $categories_model->refresh();
        $id_list=array_column($data['data'],'id');
        $count=$categories_model->where_in('parent_id',$id_list)->group_by('parent_id')->select(['parent_id'])->count();
        $count=common::array_value_key_value($count,'parent_id','count');
        return view('shop/tables',[
            'page'=>$data,
            'user_info'=>$user_info,
            'request'=>$request->all(),
            'count'=>$count,
        ]);
    }
    public function loginout(){
        cookie::forget("admin_token");
        cookie::forget("ssid");
        cookie::forget("rqid");
        redirect(index_path()."shop/login");
    }
}