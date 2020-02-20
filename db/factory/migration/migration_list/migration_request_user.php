<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/15 0015
 * Time: 下午 11:45
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_request_user extends migration
{
    public $table_name='request_user';
    public function create()
    {
        $this->db->integer('id',10,'not null',true,true);
        $this->db->string('ip',30)->commemt('访问的唯一标识');
        $this->db->string('rqid',50)->commemt('访问的id');
        $this->db->datetime('request_time')->commemt('访问的时间');
        $this->db->string('request_url',50)->commemt('访问的url');
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}