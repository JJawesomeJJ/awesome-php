<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5 0005
 * Time: 下午 4:03
 */

use db\db;

class alter_table
{
    private $con;
    public function __construct()
    {
        $user="register";
        $password="zlj19971998";
        $this->con=mysqli_connect("localhost",$user,$password,"register");
        $data="北京市，天津市，上海市，重庆市，河北省，山西省，辽宁省，吉林省，黑龙江省，江苏省，浙江省，安徽省，福建省，江西省，山东省，河南省，湖北省，湖南省，广东省，海南省，四川省，贵州省，云南省，陕西省，甘肃省，青海省，台湾省，内蒙古自治区，广西壮族自治区，西藏自治区，宁夏回族自治区，新疆维吾尔自治区，香港特别行政区，澳门特别行政区";
        $table_list=explode("，",$data);
        foreach ($table_list as $value) {
            $sql="alter table $value add column writer varchar(25)";
            $this->con->query($sql);
            }
    }
}
new alter_table();