<?php
/**
 * Created by awesome.
 * Date: 2019-06-24 09:15:28
 */
namespace app\controller\wechat;
use app\controller\controller;
use db\model\user\user;
use load\provider;
use request\request;
use system\common;

class wechat_controller extends controller
{
    public function login(request $request,user $user){
        if($user->where("id",$request->get("id"))->exist(true)){
            common::remember_me($request->get("id"));
            return [
                "csrf_token"=>$this->middlware("csrf_middleware")->sign_csrf_token(),
            ];
        }
        else{
            $this->register($request);
        }
    }
    public function register(request $request){
        $user=new user();
        $user->create([
            "id"=>$request->get("id"),
            "name"=>$request->get("nickName"),
            "head_img"=>$request->get("avatarUrl"),
            "origin"=>"wechat",
        ]);
        return ["code"=>200,"message"=>"ok", "scrf_token"=>$this->middlware("csrf_middleware")->sign_csrf_token()];
    }
}