<?php


namespace db\factory\migration\migration_list;


use db\factory\migration\migration;
use db\model\cms\Menu;

class migration_cms_menu extends migration
{
    public $table_name="CMS_MENU";
    public function create()
    {
        $this->db->integer('id',4,'not null',true,true);
        $this->db->integer('pid',4,'not null');
        $this->db->string('name',30,'not null');
        $this->db->string('url',50)->commemt("菜单指向的地址");
        $this->db->string('icon',50)->commemt("图标地址");
        $this->db->unsignedinteger("type",1,"0")->commemt("菜单的类型是否可显示的菜单");
        $this->db->string("method","20",'GET')->commemt("请求方式");
        $this->timestamp();
    }
    public function up()
    {
        $menu=new Menu();
        $id=$menu->create([
            "pid"=>0,
            "name"=>"系统设置",
            "url"=>"cms/system",
            "icon"=>"icon-shezhi",
            'method'=>'GET'
        ],false,true);
        $menu->create([
            "pid"=>$id,
            "name"=>"菜单设置",
            "url"=>"cms/system/menu",
            "icon"=>"icon-caidan",
            'method'=>'GET'
        ]);
    }
}