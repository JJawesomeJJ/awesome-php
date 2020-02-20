<?php
/**
 * Created by awesome.
 * Date: 2020-01-19 08:34:30
 * @description 用户认证中心
 */
namespace app\controller\Oauth;
use app\controller\controller;
use db\factory\soft_db;
use db\model\Oauth;
use request\request;

class OAuth_controller extends controller
{
    /**
     * @description 新增第三方认证机构
     * @param Oauth $oauth
     * @param request $request
     */
    public function add(Oauth $oauth,request $request){
        $rule=[
            'tel'=>'reqiure|is_tel|unique:Oauth',
            'web'=>'reqiure|unique:Oauth'
        ];
        $request->verifacation($rule);
        return $oauth->create($request->all());
    }
}