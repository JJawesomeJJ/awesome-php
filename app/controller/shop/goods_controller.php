<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/18 0018
 * Time: 上午 9:09
 */

namespace app\controller\shop;


use app\controller\admin_user\admin_user_controller;
use app\controller\controller;
use db\model\shop\categories;
use db\model\shop\goods;
use request\request;
use system\config\config;
use system\file;

class goods_controller extends controller
{
    public function import_goods(goods $goods){
        $file=new file();
        $file_list=$file->file_walk(config::env_path()."filesystem\goods_info");
        foreach (array_slice($file_list,0,200) as $item){
            $goods->add(json_decode($file->read_file($item),true),$file->get_file_name($item));
            $file->delete_file($item);
        }
    }
    public function goods_num(goods $goods,request $request){
        return $goods->goods_num($request->get("id"));
    }
    public function goods(goods $goods,request $request,categories $categories){
        $user_info=admin_user_controller::permission(false);
        if(empty($user_info)){
            redirect(index_path()."shop/login");
        }
        echo   view('shop/forms',[
            "page"=>$goods->get_goods($request),
            'catalog'=>$categories->catalog(),
            'user_info'=>$user_info,
            'url'=>$request->get_url(),
        ]);
    }
    public function update(goods $goods){
        ini_set('memory_limit', '1024M');
        $file=new file();
        $file_list=$file->file_walk(config::env_path()."filesystem/goods_details");
        $index=0;
        foreach ($file_list as $good){
            $index=$index+1;
            if($index>800){
                break;
            }
            $data=$file->read_file($good);
            if(strpos($data,'undefined')!==false){
                $file->delete_file($good);
                continue;
            }
            echo $good.PHP_EOL;
            $data=json_decode($data,true);
            $goods->update_info($data,explode('.',basename($good))[0]);
            $file->delete_file($good);
        }
    }
}