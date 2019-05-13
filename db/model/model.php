<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/29 0029
 * Time: 下午 6:28
 */

namespace db\model;


use db\factory\soft_db;
use system\Exception;
abstract class model
{
    protected $table_name=null;
    //定义对应的表
    protected $db=null;
    //创建数据库对象默认使用soft_db
    protected $primary_key=null;
    //设置主键
    protected $table_column_list=[];
    //设置模型对象表的字段
    protected $model_list=null;
    //模型实例
    protected $guard=[];
    //设置被保护的字段readonly
    protected $foreign_model_list=[];
    //与该模型关联的模型对象默认使用懒加载模式
    public function __construct()
    {
        $this->db=soft_db::table($this->table_name);
        //实例化数据库对象
        $this->table_column_list=$this->db->get_table_column_cache();
        //初始化表单字段
    }
    public function where(...$arr){
        if(!in_array($arr[0],$this->table_column_list)){
            new Exception("300","this_model_not_exits_key_$arr[0]");
        }
        if(count($arr)==2){
            $this->db->where($arr[0],$arr[1]);
            return $this;
        }
        if(count($arr)==3){
            $this->db->where($arr[0],$arr[2],$arr[1]);
            return $this;
        }
        new Exception("300","call_method_error_more_than_params_set_method_where");
        //设置实例化模型的条件
    }//
    public function or_where(...$arr){
        if(!in_array($arr[0],$this->table_column_list)){
            new Exception("300","this_model_not_exits_key_$arr[0]");
        }
        if(count($arr)==2){
            $this->db->or_where($arr[0],$arr[1]);
            return $this;
        }
        if(count($arr)==3){
            $this->db->or_where($arr[0],$arr[2],$arr[1]);
            return $this;
        }
        new Exception("300","call_method_error_more_than_params_set_method_where");
        //设置实例化模型的条件
    }//
    public function where_in($column_name,array $arr_condition){
        if(!in_array($column_name,$this->table_column_list)){
            new Exception("300","this_model_not_exits_key_$column_name");
        }
        $this->db->where_in($column_name,$arr_condition);
        return $this;
    } //设置实例化模型的条件
    public function or_where_in($column_name,array $arr_condition){
        if(!in_array($column_name,$this->table_column_list)){
            new Exception("300","this_model_not_exits_key_$column_name");
        }
        $this->db->or_where_in($column_name,$arr_condition);
        return $this;
    } //设置实例化模型的条件
    public function where_bettween($column_name,$min,$max,$is_not=false){
        if(!in_array($column_name,$this->table_column_list)){
            new Exception("300","this_model_not_exits_key_$column_name");
        }
        $this->db->where_between($column_name,$min,$max,$is_not);
        return $this;
    } //设置实例化模型的条件
    public function or_where_bettween($column_name,$min,$max,$is_not=false){
        if(!in_array($column_name,$this->table_column_list)){
            new Exception("300","this_model_not_exits_key_$column_name");
        }
        $this->db->or_where_between($column_name,$min,$max,$is_not);
        return $this;
    } //设置实例化模型的条件
    public function __get($name)
    {
        if(method_exists($this,$name)){
            return call_user_func([$this,$name]);
            //判断该属性是否与其他模型的名字
            //如果是设则实例化该模型并设置条件返回与当前模型管理的模型实例
        }
        if(!in_array($name,$this->table_column_list)){
            new Exception("300","this_key_not_exist_in_this_model");
        }
        if(empty($this->model_list)){
            new Exception("300","model_data_unfind");
        }
        if($this->is_1_array($this->model_list)){
            return $this->model_list[$name];
        }
        else{
            $value_list=[];
            foreach ($this->model_list as $value){
                $value_list[]=$value[$name];
            }
            return $value_list;
        }
    }//获取当前模型对应属性的值，如果这是多个模型则返回数组集合
    public function get(){
        $this->model_list=$this->db->all()->get();
        return $this;
    }//实例化模型返回数据集
    public function __set($name,$value)
    {
        if(!in_array($name,$this->table_column_list)){
            new Exception("300","this_key_not_exist_in_this_model");
        }
        if(in_array($name,$this->guard)){
            new Exception("300","this_column_has_been_protect_$name");
        }
        $this->db->set($name,$value);
        if($this->is_1_array($this->model_list)){
            $this->model_list[$name]=$value;
        }
        else{
            for ($i=0;$i<count($this->model_list);$i++){
                $this->model_list[$i][$name]=$value;
            }
        }
    }
    private function is_1_array(array $arr){
        if (count($arr) == count($arr, 1)) {
            return true;
        } else {
            return false;
        }
    }
    public function crate(array $arr){
        return $this->db->insert($arr);
    }
    public function update(){
        $this->db->update();
    }
    protected function has($model_name,$this_model_key,$foreign_model_key){
        if(!array_key_exists($model_name,$this->foreign_model_list)){
            if($this->is_1_array($this->model_list)){
                $model_object=new $model_name();
                $model_object->where($foreign_model_key,$this->model_list[$this_model_key])->get();
                $this->foreign_model_list[$model_name]=$model_object;
            }
            else{
                $condition_list=[];
                foreach ($this->model_list as $value){
                    $condition_list[]=$value[$this_model_key];
                }
                $model_object=new $model_name();
                $model_object->where_in($foreign_model_key,$condition_list)->get();
                $this->foreign_model_list[$model_name]=$model_object;
            }
        }
        return $this->foreign_model_list[$model_name];
        //实例化模型的具体方法
        //根据设置的外键设置条件
    }
    public function all(){
        return $this->model_list;
    }
    //数据被删除之后对应的模型重置
    public function delete(){
        $this->db->delete();
        $this->model_list = [];
    }
}