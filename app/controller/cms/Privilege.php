<?php
/**
 * @DESCRIPTION
 * It is a Cms Privilage System
 * We use it to control user visit CMS SYSTEM
 */

namespace app\controller\cms;


use db\model\cms\Menu;
use db\model\user\user;
use request\request;
use system\cache\cache;
use system\common;
use system\session;

class Privilege
{
    protected $config=[
        'user_key'
    ];
    public function request_control(request $request,cache $cache){
        $menu_list=$cache->get_non_exist_set("cms-menu",function (){
            $menu=(new Menu())->select(['id','url','pid']);
            return $menu->all();
        });
        $menu_list=common::array_group_by_key($menu_list,"url");
        $user_priv=$cache->get_non_exist_set("user_priv".session::get("admin")->id,function (){

        })
        if(array_key_exists($request->get_url(),$menu_list)){
            $menu_details=common::array_group_by_key($menu_list[$request->get_url()],"method");
            if(isset($menu_details[$request->request_mothod()])){
                $final_menu=$menu_details[$request->request_mothod()];
            }
        }
    }
}