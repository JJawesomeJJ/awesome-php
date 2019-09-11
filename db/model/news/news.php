<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/11 0011
 * Time: 下午 8:52
 */

namespace db\model\news;


use db\model\model;

class news extends model
{
    public $table_name="news";
    public function comment_list(){
        return $this->has("comment_list","id","url");
    }
}