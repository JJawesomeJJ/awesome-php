<?php
/**
 * Created by awesome.
 * Date: 2020-02-15 02:47:22
 */
namespace app\controller\cms;
use app\controller\controller;
use db\model\native\gift;
use request\request;
use system\cache\cache;
use system\config\config;
use system\file;
use system\session;

class NativeController extends controller
{
    /**
     * @Method GET
     * @param request $request
     * @param gift $gift
     * @return array|false|string
     */
    public function index(request $request,gift $gift){
        return view("cms/native/gift",[
            'title'=>"礼物设置",
            "gift"=>$gift->all()
        ]);
    }

    /**
     * @Method POST
     * @param request $request
     * @param gift $gift
     * @return bool
     */
    public function add_gift(request $request,gift $gift,cache $cache){
        $rules=[
            "name"=>"reqiured|noempty",
            "value_"=>"reqiured|noempty|is_number",
            "desc_"=>"reqiured|noempty",
            "fun"=>"reqiured|noempty",
            "icon"=>"reqiured|noempty"
        ];
        $request->verifacation($rules);
        $user_input=$request->except(['id']);
//        $user_input['uid']=session::get('admin')->id;
        if(!empty($request->get("icon"))){
            $file=new file();
            $user_input['icon']=$file->base64_jpeg($request->get("icon"),config::upload_path("img"),md5($request->get("icon")));
        }
        $cache->delete_key("native_gift");
        return $gift->create($user_input);
    }

    /**
     * @Method POST
     * @param request $request
     * @param gift $gift
     * @return bool
     */
    public function del_gift(request $request,gift $gift){
        return $gift->where('id',$request->get('id'))->delete();
    }

    /**
     * @Method GET
     * @param request $request
     * @param gift $gift
     * @return gift
     */
    public function details(request $request,gift $gift){
        return $gift->where('id',$request->get('id'))->all()[0];
    }
    public function edit(request $request,gift $gift,cache $cache){
        $gift_info=$request->except(['id']);
        if(!empty($request->get("icon"))){
            $file=new file();
            $gift_info['icon']=$file->base64_jpeg($request->get("icon"),config::upload_path("img"),md5($request->get("icon")));
        }else{
            unset($gift_info['icon']);
        }
        $cache->delete_key("native_gift");
        return $gift->where("id",$request->get("id"))
            ->update($gift_info);
    }
}