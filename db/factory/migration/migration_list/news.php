<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/11 0011
 * Time: 下午 8:53
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class news extends migration
{
    public $table_name="news";
    public function create()
    {
        $this->db->string("id",225,"not null",true);
        $this->db->text("content");
        $this->timestamp();
        $this->db->string("source",50);
        $this->db->string("type",30,"normal");
        $this->db->text("title");
    }
}