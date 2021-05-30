<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/14 0014
 * Time: 下午 4:45
 */

namespace db\factory\migration\migration_list;


use db\factory\migration\migration;

class migration_goods extends migration
{
    public $table_name='goods';
    public function create()
    {
        $this->db->integer('id',10,'not null',true,true);
        $this->db->integer('cid',10);
            //->foreign_key('cid','categories','id');
        $this->db->integer('comment_num',10)->commemt('评论的人数');
        $this->db->string('title',100)->commemt('商品名称');
        $this->db->string('keywords',225)->commemt('关键字');
        $this->db->string('img',225)->commemt('商品图片');
        $this->db->string('description',225)->commemt('商品描述');
        $this->db->string('details_src',100)->commemt('商品源地址');
        $this->db->integer('pv',10,0)->commemt('商品点击量');
        $this->db->string('name',100)->commemt('商品名称');
        $this->db->decimal('price',10,2)->commemt('商品价格');
        $this->db->tinyint('status',1,1)->commemt('商标状态，1为正常，2为未上架，3已下架');
        $this->db->integer('stock',10)->commemt('商品库存');
        $this->db->decimal('purchase_index',2,1)->commemt('商品的建议购买指数');
        $this->db->text("banner")->commemt("banner 图的地址 json");
        $this->db->text("goods_details")->commemt("商品的详细信息json");
        $this->db->text("goods_img")->commemt("商品的图片");
        $this->timestamp();

    }
    public function up()
    {
        // TODO: Implement up() method.
    }
}