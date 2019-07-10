<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24 0024
 * Time: 下午 2:42
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_admin_user extends migration
{
    public $table_name="admin_user_new";
    public function create()
    {
        $this->db->string("id","225","not null",true);
        $this->db->string("name","30","not null")->unique();
        $this->db->string("permission","30","admin");
        $this->timestamp();
        $this->db->string("head_img","225","not null");
        $this->db->string("sex","6","man");
        $this->db->string("password","225","not null");
        $this->db->string("email","30","not null")->unique();
        $this->db->integer("tele",11);
    }
}