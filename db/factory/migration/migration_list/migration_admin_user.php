<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/24 0024
 * Time: 下午 2:42
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;
use db\model\admin_user_new\admin_user_new;

class migration_admin_user extends migration
{
    public $table_name="admin_user_new";
    public function create()
    {
        $this->db->integer("id","225","not null",true,true);
        $this->db->string("name","30","not null")->unique();
        $this->db->string("permission","30","admin");
        $this->timestamp();
        $this->db->string("head_img","225","not null");
        $this->db->string("sex","6","man");
        $this->db->string("password","225","not null");
        $this->db->string("email","30","not null")->unique();
        $this->db->text("priv")->commemt('用户的权限');
        $this->db->integer("tele",11);
    }
    public function up()
    {
        $admin_user=new admin_user_new();
        $admin_user->create([
            "name"=>"admin",
            "password"=>md5("19971998"),
            'tele'=>'13036591969',
            'email'=>"1293777844@qq.com",
            "head_img"=>"https://dss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=1050973335,3741690257&fm=115&gp=0.jpg"
        ]);
    }
}