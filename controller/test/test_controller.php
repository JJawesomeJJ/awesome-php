<?php
/**
 * Created by awesome.
 * Date: 2019-06-09 06:38:01
 */
namespace controller\test;
use controller\admin_user\admin_user_controller;
use controller\controller;
use db\factory\soft_db;
use db\model\model_auto\model_auto;
use db\model\user\user;
use load\provider;
use request\request;
class test_controller extends controller
{
    public function test(request $request){
//        print_r($request->all());
//        return $user->get()->all();
        admin_user_controller::check_csrf_token();
        $rules=[
            "page"=>"requred|is_number",
            "limit"=>"requred|is_number"
        ];
        $request->verifacation($rules);
        $notify_list=model_auto::model("notify_list")->pager($request->get("page"),$request->get("limit"));
        if($title=$request->try_get("title")){
            $notify_list->where_like("title",$request->get("title"));
        }
        if($date=$request->try_get("date")){
            $date=explode(" - ",$date);
            $notify_list->where_bettween("created_at",$date[0]." 00:00:00",$date[1]." 00:00:00");
        }
        $is_pass_arr=[];
        if($request->try_get("is_pass")!="false"){
            $is_pass_arr[]=1;
        }
        if($request->try_get("un_pass")!="false"){
            $is_pass_arr[]=0;
        }
        if(!empty($is_pass_arr)){
            $notify_list->where_in("is_pass",$is_pass_arr);
        }
//        if($title=$request->try_get("title")){
//            $notify_list->where_like("title",$request->get("title"));
//        }
        $notify_list=$notify_list->get()->all();
        if($this->is_1_array($notify_list)){
            $data=[];
            $data[]=$notify_list;
            $notify_list=$data;
        }
        return ["code"=>0,"msg"=>"","count"=>count($notify_list),"data"=>$notify_list];
    }
}