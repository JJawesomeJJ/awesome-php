<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17 0017
 * Time: 下午 10:14
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_survey extends migration
{
    public $table_name="survey";
    public function create()
    {
        $this->db->string("writer",15,"not null")->unique();
        $this->db->string("survey_name","100","not null");
        $this->db->string("flag","30",'on_survey');
        $this->db->string("data",225);
        $this->db->string("result",225);
        $this->timestamp();
    }
}