<?php


namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_gift extends migration
{
    public $table_name="native_gift";
    public function create()
    {
        $this->db->integer("id",4,"not null",true,true);
        $this->db->string("name",20,"not null")->unique()->commemt("礼物的名字");
        $this->db->decimal("value_",10,2)->commemt("该商品的价格");
        $this->db->string("desc_",225)->commemt("礼物的描述");
        $this->db->string("icon",100)->commemt("礼物图标的地址");
        $this->db->string("fun",20)->commemt("礼物的表现程序的函数名");
        $this->db->integer('uid',4)->commemt("创建者的id");
        $this->timestamp();
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}