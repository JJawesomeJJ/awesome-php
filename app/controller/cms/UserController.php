<?php
/**
 * Created by awesome.
 * Date: 2020-02-19 10:34:39
 */
namespace app\controller\cms;
use app\controller\controller;
use db\model\admin_user_new\admin_user_new;
use request\request;

class UserController extends controller
{
    /**
     * @description 系统管理员列表
     * @param request $request
     * @param admin_user_new $user_new
     */
    public function user_list(request $request,admin_user_new $user_new){
        return view('cms/admin/user_list',['page'=>$user_new
            ->page($request->try_get("page")!=false?$request->get("page"):1,10),
            'title'=>'人员管理'
        ]);
    }
}