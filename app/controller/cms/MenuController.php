<?php
/**
 * Created by awesome.
 * Date: 2020-02-14 01:30:58
 */
namespace app\controller\cms;
use app\controller\controller;
use db\factory\soft_db;
use db\model\cms\Menu;
use extend\PHPMailer\Exception;
use request\request;
use system\config\config;

class MenuController extends controller
{
    protected $request_method=[
        'GET',
        'POST'
    ];
    /**
     * @request-method post
     * @description 添加一个目录
     * @param request $request
     * @param Menu $menu
     * @return bool
     */
    public function add_memu(request $request,Menu $menu){
        $rules=[
            "name"=>"reqiured|noempty",
            "url"=>"reqiured|noempty",
        ];
        $request->verifacation($rules);
        return $menu->add_menu($request->get("name"),str_replace("%2F","/",$request->get("url")),$request->get("icon"),$request->get('pid'));
    }

    /**
     * @Method GET
     * @param Menu $menu
     * @return array|false|string
     */
    public function index(Menu $menu){
        return view("cms/menu/menu",[
            "menu_info"=>$menu->compile_menu($menu->all()),
            "title"=>"菜单设置",
            ]);
    }

    /**
     * @method GET
     * @param request $request
     * @param Menu $menu
     */
    public function menu_info(request $request,Menu $menu){
        $params=[];
        $menu2=new Menu();
        $params["title"]="新增目录";
        if($request->try_get("id")){
            $params["menu_info"]=$menu->where("id",$request->get("id"))->get();
            $params['title']="修改目录";
        }
        $params['method']=$this->request_method;
        $params['menu_list']=$menu->compile_menu($menu2->all());
        $params['icon']=AssetsController::IconFont();
        return view("cms/menu/menu_info",$params);
    }

    /**
     * @Method POST
     * @param request $request
     * @param Menu $menu
     * @return mixed
     */
    public function edit(request $request,Menu $menu){
        $info=$request->all();
        if(!$request->try_get("type")){
            $info['type']=1;
        }
        $info["url"]=str_replace("%2F","/",$request->get('url'));
        return $menu->where("id",$request->get("id"))->update($info);
    }

    /**
     * @Method POST
     * @param request $request
     * @param Menu $menu
     * @return bool
     */
    public function del(request $request,Menu $menu){
        if($menu->where("pid",$request->get("id"))->exist(true)){
            return false;
        }
        return $menu->where('id',$request->get("id"))->delete();
    }
}