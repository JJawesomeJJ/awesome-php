<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/13 0013
 * Time: 下午 2:50
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_notify_list extends migration
{
    public $table_name="notify_list";
    public function create()
    {
        $this->timestamp();
        $this->db->string("id","225","not null",true);
        $this->db->integer("is_pass",10,"0");
        $this->db->string("publisher","30","not null");
        $this->db->string("recipient","225");
        $this->db->text("content");
        $this->db->string("title","50");
        $this->db->integer("expired","20");
        $this->db->string("notify_way","20");
        $this->db->text("show_message");
//        $this->db->foreign_key("publisher","admin_user_new","name");
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}