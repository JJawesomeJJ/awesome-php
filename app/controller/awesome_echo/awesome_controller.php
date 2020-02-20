<?php
/**
 * Created by awesome.
 * Date: 2020-01-28 12:53:49
 */
namespace app\controller\awesome_echo;
use app\controller\auth\auth_controller;
use app\controller\controller;
use extend\awesome\awesome_echo_tool;
use request\request;

class awesome_controller extends controller
{
    /**
     * @description 绑定用户与user_id与fd
     */
    public function author(request $request,awesome_echo_tool $awesome_echo_tool){
        return $awesome_echo_tool->bind_id($request->get('fd'),auth_controller::auth(),$request->get('user_token'));
    }
}