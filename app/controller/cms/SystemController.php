<?php
/**
 * Created by awesome.
 * Date: 2020-02-12 08:34:23
 */
namespace app\controller\cms;
use app\controller\controller;
use db\model\cms\Menu;
use request\request;
use system\config\config;

class SystemController extends controller
{
    public function add_menu_page(){

    }
    public function index(Menu $menu){
        $menu_info=$menu->compile_menu($menu->all());
        return view("cms/menu/menu",[
            "menu"=>$menu_info,
            'title'=>"礼物设置"
        ]);
    }
}