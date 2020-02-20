<?php
/**
 * Created by awesome.
 * Date: 2020-01-29 05:15:12
 */
namespace app\controller\channel;
use app\controller\auth\auth_controller;
use app\controller\controller;
use app\Event\native_push_event;
use extend\awesome\awesome_echo_tool;
use request\request;
use system\class_define;
use system\config\channel_config;
use system\Exception;
use system\LuaScript;

class channel_controller extends controller
{
    public function __construct(request $request)
    {
        parent::__construct();
        awesome_echo_tool::safe_check($request);
    }

    /**
     * @description 用户加入频道
     * @param request $request
     * @param awesome_echo_tool $awesome_echo_tool
     */
    public function add_native_channel(request $request,awesome_echo_tool $awesome_echo_tool){
        $awesome_echo_tool->user_add_channel($request->get('fd'),$request->get('channel_name'));
        $user_name="系统";
        $message=$request->all();
        $message['user_name']=$user_name;
        $message['msg']=auth_controller::auth()."来了";
        $message['online']=$awesome_echo_tool->channel_online_user($request->get("channel_name"));
        event(new native_push_event($message));
        return ['code'=>200,'message'=>"suceess add channel"];
    }

    /**
     * @description 用户离开频道
     * @param request $request
     * @param awesome_echo_tool $awesome_echo_tool
     */
    public function leave_native_channel(request $request,awesome_echo_tool $awesome_echo_tool){
        $awesome_echo_tool->user_leave_channel($request->get('fd'),$request->get("channel_name"));
    }
}