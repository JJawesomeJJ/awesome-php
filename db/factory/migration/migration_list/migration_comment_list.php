<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17 0017
 * Time: 下午 9:54
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_comment_list extends migration
{
    public $table_name="comment_list";
    public function create()
    {
        $this->db->string("id","50","not null",true);
        $this->db->string("url","100","not null");
        $this->db->string("reply_who",100,"not null");
        $this->db->string("user_id","30","not null");
        $this->db->string("comment_content",225,"not null");
        $this->db->string("reply_id","50","not null");
        $this->db->text("likes");
        $this->timestamp();
    }
}