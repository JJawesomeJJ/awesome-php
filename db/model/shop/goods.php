<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/18 0018
 * Time: 上午 8:50
 */

namespace db\model\shop;


use db\model\model;
use request\request;
use system\common;

class goods extends model
{
    protected $table_name='goods';
    public function add(array $params,$name){
        $categories=new categories();
        $id=$categories->get_name_id($name);
        if(!is_string($id)){
            $id=$id[0];
        }
        $goods=new goods();
        if($goods->where('cid',$id)->exist()){
            return;
        }
        foreach ($params as $key=>$value){
            $params[$key]['cid']=$id;
            $params[$key]['purchase_index']=$params[$key]['purchase index'];
            $params[$key]['img']=$params[$key]['src'];
            $params[$key]['comment_num']=$params[$key]['comment'];
            $params[$key]['price']=str_replace('￥','',$params[$key]['price']);
            if(!is_numeric(trim($params[$key]['purchase_index']))){
                unset($params[$key]['purchase_index']);
            }
            $params[$key]['comment_num']=str_replace("+","",$params[$key]['comment_num']);
            if(strpos($params[$key]['comment_num'],'万')!==false){
                $params[$key]['comment_num']=str_replace("万","",$params[$key]['comment_num'])*10000;
            }
            unset($params[$key]['src']);
            unset($params[$key]['comment']);
            unset($params[$key]['purchase index']);
        };
        foreach ($params as $item){
            $this->create($item);
        }
    }
    public function goods_num(array $id_params){
        if(common::is_1_array($id_params)){
            return ['name'=>$this->where('cid',$id_params['id'])->group_by('cid')->count()];
        }
        else{
            $id_list=array_column($id_params,'id');
            $data=$this->select(['cid'])->where_in('cid',$id_list)->group_by('cid')->count();
            return common::array_value_key_value($data,'cid','count');
        }
    }
    public function categories(){
        return $this->has('categories','cid','id');
    }
    public function get_goods(request $request){
        $this->categories()->select(['name','id']);
        $catalog=new categories();
        if($request->try_get('name')){
            if(in_array($request->get('name'),array_keys(categories::catalog_list()))){
                $parents_id_list=$catalog->select(['id'])->where_in('name',categories::catalog_list()[$request->get('name')])->all();
                $this->refresh();
                return $this->where_in('parents_id',$parents_id_list)-$this->page($request->get('page',1),12);
            }
            else{
                $catalog_info=$catalog->select(['id','level'])->where('name',$request->get('name'))->get();
                $id=$catalog_info->id;
                $level=$catalog_info->level;
                $catalog_info=$catalog_info->all();
                if($level==1){
                    $catalog->refresh();
                    $id=$catalog->where('parent_id',$id)->select(['id'])->id;
                }
                if(is_array($id)) {
                    return $this->where_in('cid', $id)->page($request->get('page', 1), 12);
                }
                else{
                    return $this->where('cid', $id)->page($request->get('page', 1), 12);
                }
            }
        }
        return $this->page($request->get('page',1),12);
    }
    public function update_info(array $goods_info,$id){
        $this->where('id',$id)->update([
            "name"=>$goods_info['goods_details']['商品名称'],
            "banner"=>json_encode($goods_info['banner']),
            "goods_details"=>json_encode($goods_info['goods_details']),
            "goods_img"=>json_encode($goods_info['goods_img'])
        ]);
    }
}