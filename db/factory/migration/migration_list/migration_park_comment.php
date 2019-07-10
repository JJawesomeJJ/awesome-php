<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/23 0023
 * Time: 下午 5:15
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_park_comment extends migration
{
    public $table_name="park_comment";
    public function create()
    {
        $this->db->string("id","50","not null",true);
        $this->db->string("url","100","not null");
        $this->db->string("reply_who",100,"not null");
        $this->db->string("user_name","30","not null");
        $this->db->string("comment_content",225,"not null");
        $this->db->string("reply_id","50","not null");
        $this->timestamp();
    }
}