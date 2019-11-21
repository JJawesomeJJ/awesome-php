<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17 0017
 * Time: 上午 10:47
 */

namespace db\factory\migration;


use db\factory\soft_db;
use system\cache\cache;
use system\Exception;

abstract class migration
{
    public $db;
    public $table_name;
    public function __construct()
    {
        if(is_null($this->table_name))
        {
            new Exception("400","table_name_undefined");
        }
        if(!is_array($this->table_name)){
            $this->db=soft_db::table($this->table_name);
        }
    }
    abstract function create();
    public function timestamp(){
        $this->db->datetime("created_at");
        $this->db->timestamp("updated_at");
    }
    public function create_(){
        $return_value=[];
        if(is_array($this->table_name)) {
            foreach ($this->table_name as $table) {
                $this->db=soft_db::table($table);
                $this->create();
                $result = $this->db->create();
                $return_value[$table]=$result;
                if($result) {
                    $this->db->get_table_column_cache(true);
                }
            }
        }
        else{
            $this->db=soft_db::table($this->table_name);
            $this->create();
            $result = $this->db->create();
            $return_value[$this->table_name]=$result;
            if($result) {
                $this->db->get_table_column_cache(true);
            }
        }
        return $return_value;
    }
    public function refresh(){
        $return_arr=[];
        if(is_array($this->table_name)){
            foreach ($this->table_name as $value){
                $this->db=soft_db::table($value);
                $return_arr[$value]=$this->db->delete();
            }
        }
        else{
            $this->db=soft_db::table($this->table_name);
            $return_arr[$this->table_name]=$this->db->delete();
        }
        return $return_arr;
    }
    public function update(){
        $return_arr=[];
        if(is_array($this->table_name)){
            foreach ($this->table_name as $value){
                $this->db=soft_db::table($value);
                $this->create();
                $result=$this->db->update_table_filed();
                if($result){
                    $this->db->get_table_column();
                }
                $return_arr[$value]=$result;
            }
        }
        else{
            $this->db=soft_db::table($this->table_name);
            $this->create();
            $result=$this->db->update_table_filed();
            if($result){
                $this->db->get_table_column();
            }
            $return_arr[$this->table_name]=$result;
        }
        return $return_arr;
    }
    public function drop(){
        $return_arr=[];
        if(is_array($this->table_name)){
            foreach ($this->table_name as $value){
                $this->db=soft_db::table($value);
                $return_arr[$value]=$this->db->drop();
            }
        }
        else{
            $this->db=soft_db::table($this->table_name);
            $return_arr[$this->table_name]=$this->db->drop();
        }
        return $return_arr;
    }
}