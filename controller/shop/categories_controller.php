<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/17 0017
 * Time: 下午 10:03
 */

namespace controller\shop;


use controller\controller;
use db\model\shop\categories;
use db\model\shop\goods;
use request\request;

class categories_controller extends controller
{
    public function categories(request $request,categories $categories,goods $goods){
        $data=$categories->get_children_num($request->get('id'));
//        return $data;
        $goods_num=$goods->goods_num($data);
        foreach ($goods_num as $index=>$item){
            foreach ($data as $key=>$value){
                if($value['id']==$index){
                    $data[$key]['count']=$item;
                }
            }
        }
        return $data;
    }
    public function parents(categories $categories){
    }
    public function get_children_catalog(request $request,categories $categories){
        return $categories->get_children_list($request);
    }
}