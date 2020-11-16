<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/29 0029
 * Time: 下午 6:28
 */

namespace db\model;


use db\factory\soft_db;
use system\common;
use system\Exception;

abstract class model
{
    protected $total=null;
    protected $table_name=null;
    //定义对应的表
    /**
     * @var soft_db|null
     */
    public $db=null;
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
    protected $relationship=[];
    //保存的关联模型的数据
    //["表名"=>["this_model_key"=>"","foreign_model_key"=>]]
    protected $is_load_data=false;
    //是否使用get加载模型的数据
    protected $permitted_set_table_name_list=[];
    //由于在使用set_table_name时可能有外部输入,所有可能被恶意输入导致数据的信息全部暴露
    //所以在有可能使用set_table_name 的方法的模型中重载$set_table_list防止被恶意输入
    protected $enable_query_cache=false;//是否开启查询缓存
    private $is_read_cache=false;//是否在查询是预先读取缓存
    public function __construct()
    {
        $this->db=soft_db::table($this->table_name);
        $this->refresh();
        //实例化数据库对象
        $this->table_column_list=$this->db->get_table_column_cache(false);
        //初始化表单字段
    }

    /**
     * 获取某个字段的中文名
     * @param $field
     * @return mixed
     */
    public function getFiledAttribute($field){
        return self::attributes()[$field]??$field;
    }
    /**
     * 配置字段中文名称
     * @return array
     */
    public static function attributes(){
        return [

        ];
    }
    /**
     * @description 设置为从主库读取数据
     * @return $this
     * @throws \Exception
     */
    public function ReadMaster(){
        $this->db->ReadMaster();
        return $this;
    }

    /**
     * @description 切换为自动读取数据
     * @return $this
     */
    public function SetReadAuto(){
        $this->db->SetReadAuto();
        return $this;
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
    public function count($is_refresh=false,$column_name="*"){
        $this->db->count($column_name);
        $result=$this->db->get($is_refresh);
        array_pop($this->db->query_list);
        if($this->is_1_array($result)){
            if(empty($result)){
                return [];
            }
            else{
                if(count($result)>1){
                    return $result;
                }
                return $result['count'];
            }
        }
        return $result;
    }
    public function refresh(){
        $this->model_list=null;
        $this->is_load_data=false;
        $this->db->refresh();
        return $this;
    }

    /**
     * @description 根据现有的条件重载session
     */
    public function reload(){
        $this->model_list=null;
        $this->is_load_data=false;
        $this->get();
        return $this;
    }
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
        if($this->is_load_data==false){
            $this->get();
        }
        if(method_exists($this,$name)){
            return call_user_func([$this,$name]);
            //判断该属性是否与其他模型的名字
            //如果是设则实例化该模型并设置条件返回与当前模型管理的模型实例
        }
        if(!in_array($name,$this->table_column_list)){
            new Exception("300","$name._this_key_not_exist_in_".get_class($this));
        }
        if(empty($this->model_list)){
            new Exception("300","model_data_unfind");
        }
        if(count($this->model_list)==1){
            if(!isset($this->model_list[0])||empty($this->model_list[0])){
                new Exception("300","model_data_unfind");
            }
            return $this->model_list[0][$name];
        }
        else{
            $value_list=[];
            foreach ($this->model_list as $value){
                $value_list[]=$value[$name];
            }
            return $value_list;
        }
    }//获取当前模型对应属性的值，如果这是多个模型则返回数组集合
    public function get($is_refresh=false){
        if(empty($this->db->get_select_column())) {
            $this->db->all();
        }
        if($this->enable_query_cache&&$this->is_read_cache){
            $data=$this->db->first_cache("forever",$is_refresh);
            $this->is_read_cache=false;
        }else {
            $data = $this->db->get($is_refresh);
        }
        if($this->is_1_array($data)&&!empty($data)){
            $this->model_list=[$data];
        }
        else{
            $this->model_list=$data;
        }
        $this->is_load_data=true;
        return $this;
    }//实例化模型返回数据集
    public function select(array $filed){
        $this->db->select($filed);
        return $this;
    }
    public function __set($name,$value)
    {
        if(!in_array($name,$this->table_column_list)){
            new Exception("300","this_key_not_exist_in_this_model");
        }
        if(in_array($name,$this->guard)){
            new Exception("300","this_column_has_been_protect_$name");
        }
        if($this->is_load_data==false){
            $this->get();
            $this->is_load_data=true;
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
    public function limit($start_row,$num){
        $this->db->limit($start_row,$num);
        return $this;
    }
    private function is_1_array(array $arr){
        if (count($arr) == count($arr, 1)) {
            return true;
        } else {
            return false;
        }
    }
    public function update(array $new_array=[]){
        foreach ($new_array as $key=>$value){
            if(!in_array($key,$this->table_column_list)){
                continue;
            }
            if(in_array($key,$this->guard)) {
                continue;
            }
            if(!empty($this->model_list)) {
                $this->$key = $value;
            }
            else{
                $this->db->set($key,$value);
            }
        }
        if($this->enable_query_cache){
            $this->db->flush_cache();
        }
        return $this->db->update();
    }
    public function all_with_foreign($foreign_model_name,$this_table_filed=[],$foreign_table_filed=[],$foreign_table_name=false){
        if(!method_exists($this,$foreign_model_name)){
            new Exception("400","$foreign_model_name undefine");
        }
        $foreign_model_obejct=null;
        $this_model_list=$this->all($this_table_filed);
        if($foreign_table_name!=false){
            $foreign_model_obejct=$this->$foreign_table_name($foreign_table_name);
        }
        else{
            $foreign_model_obejct=$this->$foreign_model_name();
        }
        $relationship=$this->relationship[$foreign_model_obejct->get_table_name];
        if(!in_array($relationship["this_model_key"],$this_table_filed)&&!empty($this_table_filed)){
            $this_table_filed[]=$relationship["this_model_key"];
        }
        if(!in_array($relationship["foreign_model_key"],$foreign_table_filed)&&!empty($foreign_table_filed)){
            $foreign_table_filed[]=$relationship["foreign_model_key"];
        }
        $foreign_model_list=common::array_group_by_key($foreign_model_obejct->all($foreign_table_filed),$relationship["foreign_model_key"]);
        $len=count($this_model_list);
        if(!$this->is_1_array($this_model_list)) {
            for ($i = 0; $i < $len; $i++) {
                $key = $this_model_list[$i][$relationship["this_model_key"]];
                if (array_key_exists($relationship["this_model_key"], $this_model_list[$i])&&array_key_exists($key,$foreign_model_list)) {
                    $this_model_list[$i][$foreign_model_name] = $foreign_model_list[$key];
                }
            }
        }
        else{
            $key = $this_model_list[$relationship["this_model_key"]];
            if (array_key_exists($relationship["this_model_key"], $this_model_list)&&array_key_exists($key,$foreign_model_list)) {
                $this_model_list[$foreign_model_name] = $foreign_model_list[$key];
            }
        }
        return $this_model_list;
    }
    public function get_table_name(){
        return $this->table_name;
    }
    protected function is_load_data(){
        if($this->is_load_data==false){
            $this->get();
            $this->is_load_data=true;
        }
    }//数据是否加载
    protected function has($model_name,$this_model_key,$foreign_model_key,$set_table_name=false){
        if(!array_key_exists($model_name,$this->foreign_model_list)){
            $model_object=make($model_name);
            if($set_table_name!=false){
                $model_object->set_table_name($set_table_name);
            }
            $this->relationship[$model_object->get_table_name()]=[
                "this_model_key"=>$this_model_key,
                "foreign_model_key"=>$foreign_model_key
            ];
            if(is_null($this->model_list)){
                $this->foreign_model_list[$model_name]=$model_object;
                $this->relationship[$model_object->get_table_name()]["status"]=false;
                return $model_object;
            }
            if(!is_null($this->model_list)&&$this->is_1_array($this->model_list)){
                $model_object->where($foreign_model_key,$this->model_list[$this_model_key]);
                $this->foreign_model_list[$model_name]=$model_object;
            }
            else{
                $condition_list=[];
                foreach ($this->model_list as $value){
                    $condition_list[]=$value[$this_model_key];
                }
                $model_object->where_in($foreign_model_key,$condition_list);
                $this->relationship[$model_object->get_table_name()]["status"]=true;
                $this->foreign_model_list[$model_name]=$model_object;
            }
        }
        return $this->foreign_model_list[$model_name];
        //实例化模型的具体方法
        //根据设置的外键设置条件
    }
    protected function init_foreign_model($model_object){
        $this->is_load_data();
        $table_name=$model_object->get_table_name();
        return $model_object->where_in($this->relationship[$table_name]['foreign_model_key'],array_column($this->model_list,$this->relationship[$table_name]['this_model_key']))->get()->all();
    }
    public function set_table_name($table_name){
        if(!empty($this->permitted_set_table_name_list)){
            if(!in_array($table_name,$this->permitted_set_table_name_list)){
                new Exception("403","danger input");
            }
        }//防止恶意输入
        $this->table_name=$table_name;
        $this->db=soft_db::table($table_name);
        return $this;
    }
    public function find($index){
        $this->limit($index,1);
//        return $this->get();
        $data=$this->all();
        if(isset($this->all()[0])){
            return $data[0];
        }
        else{
            return [];
        }
    }
    public function all(array $filed=[],$expect=false){
        if($this->is_load_data==false){
            $this->get();
            $this->is_load_data=true;
        }
        if(!empty($this->foreign_model_list)){
            foreach ($this->foreign_model_list as $key=>$value){
                if($this->relationship[$value->get_table_name()]['status']==false){
                    $data=$this->init_foreign_model($value);
                }
                else {
                    $data = $value->all();
                }
                if(empty($data[0])){
                    return [];
                }
                $data=common::array_group_by_key($data,$this->relationship[$value->get_table_name()]['foreign_model_key']);
                foreach ($this->model_list as $index=>$item){
                    if(array_key_exists($item[$this->relationship[$value->get_table_name()]['this_model_key']],$data)){
                        $this->model_list[$index][$value->get_table_name()]=$data[$item[$this->relationship[$value->get_table_name()]['this_model_key']]];
                    }
                }
            }
        }
        if(empty($this->model_list)){
            return [];
        }
        if(empty($filed)) {
            if(!$this->is_1_array($this->model_list)) {
                return $this->model_list;
            }
            else{
                return $this->model_list;
            }
        }
        if ($expect){
            $filed=array_diff($this->table_column_list,$filed);
        }
        $filed_list=[];
        if(!empty($this->model_list[0])) {
            foreach ($this->model_list as $model) {
                $data = [];
                foreach ($filed as $item) {
                    if(!in_array($item,$this->table_column_list)){
                        new Exception("400","this_key_not_exist");
                    }
                    $data[$item] = $model[$item];
                }
                $filed_list[] = $data;
            }
        }
        return $filed_list;
    }
    public function delete(){
        $result=$this->db->delete();
        if($this->enable_query_cache){
            $this->db->flush_cache();
        }
        if($result) {
            $this->model_list = [];
            return true;
        }
        return false;
    }
    public function create(array $filed_arr,$is_auto_id=false,$return_id=true){
        if (in_array("created_at", $this->table_column_list)) {
            if($this->is_1_array($filed_arr)) {
                $filed_arr["created_at"] = date("Y-m-d H:i:s");
            }
            else{
                foreach ($filed_arr as $key=>$value){
                    $filed_arr[$key]['created_at']=date("Y-m-d H:i:s");
                }
            }
        }
        if($is_auto_id){
            $filed_arr["id"]=md5(microtime(true).common::rand(4));
        }
        $result=$this->db->insert($filed_arr);
        if($this->enable_query_cache){
            $this->db->flush_cache();
        }
        if($return_id){
            if($result){
                return $this->db->get_insert_id();
            }
            else{
                return false;
            }
        }
        return $result;
    }
    public function first_cache(){
        $this->is_read_cache=true;
        return $this;
    }
    public function where_like($column_value,$condition_value){
        $this->db->where_like($column_value,"%$condition_value%");
        return $this;
    }
    public function page($page,$limit,$page_num=10,$filed=[],$expect=false){
        if(!is_numeric($page)||!is_numeric($limit)){
            new Exception(400,"page_required_params_is_number");
        }
        $count=$this->count();
        $this->limit(($page-1)*$limit,$limit);
        $data=[
            'total'=>$count,
            'current_page'=>$page,
        ];
        $data['page_total']=ceil($count/$limit);
        if($page+1<=$data['page_total']){
            $data['next_page']=$page+1;
        }
        if($page-1>0){
            $data['pre_page']=$page-1;
        }
        $page_list=[];
        $next_num=0;
        $pre_num=0;
        $request=make('request');
        for ($i=1;$i<=($page_num/2);$i++){
            if(($page-$i)>0){
                $pre_num++;
                $page_list[]=$page-$i;
            }
            else{
                break;
            }
        }
        for ($i=1;$i<=$page_num/2;$i++){
            if(($page+$i)<=$data['page_total']){
                $next_num++;
                $page_list[]=$page+$i;
            }else{
                break;
            }
        }
        if(count($page_list)<$page_num&&count($page_list)>0){
            $end=max($page_list);
            for($i=1;$i<($page_num+2-count($page_list));$i++){
                if(($end+$i)<$data['page_total']){
                    $page_list[]=$end+$i;
                }
                else{
                    break;
                }
            }
        }
        if(count($page_list)<$page_num&&count($page_list)>0){
            $frist=min($page_list);
            for($i=1;$i<($page_num+2-count($page_list));$i++){
                if(($frist-$i)>0){
                    $page_list[]=$frist-$i;
                }
                else{
                    break;
                }
            }
        }
        $request=make('request');
        if(count($page_list)>0) {
            $page_list[] = $page;
        }
        //current_page
        sort($page_list);
        $data['page_list']=array_unique($page_list);
        $model_data=$this->get()->all($filed,$expect);
        if(empty($model_data[0])){
            $model_data=[];
        }
        $data['data']=$model_data;
        return $data;
    }
    public function pager($page,$limit){
        $this->db->count('*');
        $count=$this->db->get(false)['count'];
        $this->limit(($page-1)*$limit,$limit);
        $this->total=$count;
        return $this;
    }
    public function exist($is_refresh=false){
        if(empty($this->all()[0])){
            if($is_refresh){
                $this->db->refresh();
            }
            return false;
        }
        if($is_refresh){
            $this->db->refresh();
        }
        return true;
    }
    public function transactions(\Closure $things,$sucess=null,$fail=null){
        $this->db->transactions($things,$sucess,$fail);
    }
    public function __toString()
    {
        $this->is_load_data();
        return json_encode($this->model_list);
    }
    public function group_by($filed){
        $this->db->group_by($filed);
        return $this;
    }
    public function __toArray(){
        if($this->is_load_data=false){
            return $this->get()->all();
        }
        else{
            return $this->model_list;
        }
    }
}