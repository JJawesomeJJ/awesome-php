<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/8 0008
 * Time: 下午 5:33
 */

namespace db\factory\migration\migration_list;



use db\factory\migration\migration;

class migration_comment_likes extends migration
{
    public $table_name="news_likes";
    public function create()
    {
        $this->db->integer("id",10,'not null',true,true);
        $this->db->text("user_id",true);
        $this->db->string("news_id",225,"not null")->foreign_key('news_id','news','id');
        $this->timestamp();
    }
}