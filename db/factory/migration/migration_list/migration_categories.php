<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/12 0012
 * Time: 下午 5:07
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_categories extends migration
{
    public $table_name="categories";
    public function create()
    {
        $this->db->integer('id',10,'not null',true,true);
        $this->db->string('name',50);
        $this->db->string('parent_id',10);
        $this->db->integer('is_directory',1,1)->commemt('1 默认有子目录');
        $this->db->integer('level',5);
        $this->db->string('path',10);
    }
}