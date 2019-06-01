<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17 0017
 * Time: 上午 10:47
 */

namespace db\factory\migration;


use db\factory\soft_db;
use system\Exception;

abstract class migration
{
    public $db;
    public $table_name;
    public function __construct()
    {
        if(is_null($this->table_name))
        {
            new Exception("400",$this->table_name);
        }
        $this->db = soft_db::table($this->table_name);
    }
    abstract function create();
    public function timestamp(){
        $this->db->datetime("created_at");
        $this->db->timestamp("updated_at");
    }
    public function create_(){
        $this->db->create();
    }
    public function refresh(){
        $this->db->delete();
    }
}