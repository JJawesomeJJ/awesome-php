<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/16 0016
 * Time: 下午 10:35
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_titang_theme extends migration
{
    public $table_name="titang_theme";
    public function create()
    {
        $this->db->string("id",50,"not null",true);
        $this->db->text("title");
        $this->db->text("morning");
        $this->db->text("noon");
        $this->db->text("afternoon");
        $this->db->text("night");
        $this->db->string("creator","30","not null");
        $this->timestamp();
        $this->db->foreign_key("creator","admin_user_new","name");
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}