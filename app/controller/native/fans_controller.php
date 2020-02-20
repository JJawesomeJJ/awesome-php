<?php
/**
 * Created by awesome.
 * Date: 2020-02-07 02:24:37
 */
namespace app\controller\native;
use app\controller\auth\auth_controller;
use app\controller\controller;
use db\model\native\follow;
use request\request;

class fans_controller extends controller
{
    /**
     * @description 获取我关注的人
     * @param follow $follow
     * @return array
     */
    public function follow(follow $follow){
        return $follow->get_my_follows_info(auth_controller::auth(true,'id'));
    }

    /**
     * @description 获取我的粉丝
     * @param follow $follow
     * @return array
     */
    public function fans(follow $follow){
        return $follow->get_follow_me_info(auth_controller::auth(true,'id'));
    }

    /**
     * @description 添加关注
     * @param follow $follow
     * @param request $request
     * @return bool
     */
    public function follow_user(follow $follow,request $request){
        return $follow->add_follow(auth_controller::auth(true,'id'),$request->get('uid'));
    }

    /**
     * @description 取消关注
     * @param follow $follow
     * @param request $request
     * @return bool
     */
    public function remove_follow(follow $follow,request $request){
        return $follow->remove_follow(auth_controller::auth(true,'id'),$request->get('uid'));
    }
}