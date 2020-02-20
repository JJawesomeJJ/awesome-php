<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/10 0010
 * Time: 下午 5:24
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_native extends migration
{
    public $table_name="native";
    public function create()
    {
        $this->db->integer('id',10,'not null',true,true);
        $this->db->string('user_id',10,'not null')->commemt('主播的id');
        $this->db->string('type',50)->commemt('直播的类型');
        $this->db->string('play',50)->commemt('直播地址');
        $this->db->string('cover',50)->commemt('直播的封面图');
        $this->db->integer("online_max",4,0)->commemt('直播在线的峰值人数');
        $this->db->text("gift_list")->commemt("礼物清单");
        $this->db->datetime("end_at")->commemt("结束时间");
        $this->db->unsignedinteger('status',1,1)->commemt("1未结算，2已结算");
        $this->timestamp();
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}