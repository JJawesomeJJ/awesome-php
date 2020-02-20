<?php


namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_account extends migration
{
    public $table_name="TB_account";
    public function create()
    {
        $this->db->integer("id",10,"not null",true,true);
        $this->db->integer("goods_id",4,"not null");
        $this->db->decimal("amount",10,2)->commemt("这笔账单的金额");
        $this->db->integer("uid",4)->commemt("用户的id");
        $this->db->unsignedinteger('num',4,'1')->commemt("商品数量");
        $this->timestamp();
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}