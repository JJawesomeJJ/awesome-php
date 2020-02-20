<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/2/6 0006
 * Time: 下午 11:47
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_follow extends migration
{
    public $table_name='follow';
    public function create()
    {
        $this->db->integer("id",4,'not null',true,true);
        $this->db->integer("uid",4,'not null')->commemt("关注者的主键");
        $this->db->integer("follow_uid",4,'not null')->commemt("被关注者的id");
        $this->db->string('unique_id_',50,'not null')->unique()->commemt("唯一复合键md5");
        $this->timestamp();
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}