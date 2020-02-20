<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/27 0027
 * Time: 上午 10:44
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_park_list extends migration
{
    public $table_name=["北京市", "广东省", "山东省", "江苏省", "河南省", "上海市", "河北省", "浙江省", "香港特别行政区", "陕西省", "湖南省", "重庆市", "福建省", "天津市", "云南省", "四川省", "广西壮族自治区", "安徽省", "海南省", "江西省", "湖北省", "山西省", "辽宁省", "台湾省", "黑龙江", "内蒙古自治区", "澳门特别行政区", "贵州省", "甘肃省", "青海省", "新疆维吾尔自治区", "西藏区", "吉林省", "宁夏回族自治区"];
    public function create()
    {
        $this->db->string("city",30,"not null");
        $this->db->string("road",30,"not null");
        $this->db->string("tele",20);
        $this->db->text("contacts");
        $this->db->text("owner_info")->commemt("用户的描述");
        $this->db->string("id",50,"not null",true);
        $this->db->string("distrist",30);
        $this->db->string("verified",10,'0')->commemt("是否验证默认为0表示未验证");
        $this->db->string("writer",30,"not null");
        $this->db->string("park_name",30);
        $this->db->string("latitude_longitude","40","not null")->unique();
    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}