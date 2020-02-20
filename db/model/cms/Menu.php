<?php
/**
 * Created by awesome.
 * Date: 2020-02-12 08:10:27
 */
namespace db\model\cms;
use db\model\model;
class Menu extends model
{
    protected $table_name="cms_menu";

    /**
     * @description 将menu数据转化为树状
     * @param array $menu_list
     * @param int $pid
     * @return array
     */
    public function compile_menu(array $menu_list,int $pid=0){
        $result=[];
        foreach ($menu_list as $item){
            if($item['pid']==$pid){//type为0表示为可以显示的菜单
                $children=$this->compile_menu($menu_list,$item['id']);
                if(!empty($children)){
                    $item['children']=$children;
                }
                $result[]=$item;
            }
        }
        return $result;
    }

    /**
     * @description 添加一条菜单
     * @param string $name
     * @param string $url
     * @param string $icon_path
     * @param int $pid
     * @return bool
     */
    public function add_menu(string $name,string $url,string $icon_path,int $pid){
        return $this->create([
            "name"=>$name,
            "url"=>$url,
            "icon"=>$icon_path,
            "pid"=>$pid
        ],false,true);
    }
}