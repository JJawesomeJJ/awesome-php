<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/24 0024
 * Time: 下午 4:22
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_user extends migration
{
    public $table_name="user";
    public function create()
    {
        $this->db->string("id",50,"not null",true);
        $this->db->string("name",30,"not null")->unique();
        $this->db->string("password",100);
        $this->db->string("sex","6","man")->commemt("性别");
        $this->db->string("email",30)->unique();
        $this->db->text("head_img")->commemt("头像");
        $this->db->string("remenber_token",100);
        $this->db->string("origin",30,"titang_web")->commemt("用户来源");
        $this->db->integer("tele",11)->unique();
        $this->db->integer("email_status",1,0)->commemt("邮箱是否验证0即为否1为已验证");
        $this->db->integer("tele_status",1,0)->commemt("手机是否验证0即为否1为已验证");
        $this->timestamp();
    }
}