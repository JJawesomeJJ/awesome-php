<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/13 0013
 * Time: 下午 3:16
 */
namespace db\model\model_auto;
use db\model\model;

class model_auto extends model
{
    protected static $model_list_=[];
    public function __construct($table_name)
    {
        $this->table_name=$table_name;
        parent::__construct();
    }
    public static function model($table_name){
        if(array_key_exists($table_name,self::$model_list_)){
            return self::$model_list_[$table_name];
        }
        else{
            $model_object=new model_auto($table_name);
            self::$model_list_[$table_name]=$model_object;
            return $model_object;
        }
    }
}