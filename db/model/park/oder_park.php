<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/1 0001
 * Time: 上午 10:47
 */

namespace db\model\park;


use db\model\model;

class oder_park extends model
{
    protected $table_name="oder_park";
    public function map($table_name){
        return $this->has("map","park_id","id",$table_name);
    }
}