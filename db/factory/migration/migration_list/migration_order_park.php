<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/23 0023
 * Time: 下午 8:01
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_order_park extends migration
{
    public $table_name="oder_park";
    public function create()
    {
        $this->timestamp();
        $this->db->string("id",50,"not_null",true);
        $this->db->string("order_id",50)->unique();
        $this->db->string("status","30","unpaid");
        $this->db->datetime("end_at");
        $this->db->decimal("amount_total",20,2);
        $this->db->string("user_id",50,"not null");
        $this->db->string("pay_way",30);
        $this->db->string("park_id",50,"not null");
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}