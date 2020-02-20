<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/12 0012
 * Time: 下午 8:50
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_OAuth extends migration
{
    public $table_name="OAuth";
    public function create(){
        $this->db->integer('id',10);
        $this->db->string('user_key',50);
        $this->db->integer('tel',11);
        $this->db->integer('status',1);
        $this->db->string('web',50);
        $this->timestamp();
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}