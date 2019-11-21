<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/12 0012
 * Time: 下午 5:24
 */

namespace db\model\shop;


use db\model\model;
use request\request;
use system\common;

class categories extends model
{
    public $table_name="categories";
    public function add_categories(array $categories_list,$level=1,$path='-',$parent_id=''){
        foreach ($categories_list as $key=>$value){
            $id=$this->create(['name'=>$value['name'],"level"=>$level,"path"=>$path,'parent_id'=>$parent_id],false,true);
            if(isset($value['children'])){
                $this->add_categories($value['children'],$level+1,$path.'-'.$id,$id);
            }
        }
    }
    public function get_categories(){
        $data=$this->get()->all(['name','parent_id','id']);
        $arr=$this->get_children($data);
        return $arr;
    }
    public function get_children($list,$parents_id=''){
        $arr=[];
        foreach ($list as $key=>$value){
            if($value['parent_id']==$parents_id){
                $chidren=$this->get_children($list,$value['id']);
                if(!empty($chidren)){
                    $value['chidren']=$chidren;
                    $arr[]=['name'=>$value['name'],'chidren'=>$chidren];
                }
                else {
                    $arr[] =['name'=>$value['name']];
                }
            }
        }
        return $arr;
    }
    public function get_children_num($id){
        return $this->where('parent_id',$id)->all(['name',"id"]);
    }
    public function get_name_id($name){
        if(is_string($name)) {
            $name = str_replace('&', '/', $name);
            return $this->where('name', $name)->select(['id'])->get()->id;
        }
    }
    public static function catalog_list(){
        $catalog=["家用电器"=>["电视","空调","洗衣机","冰箱","厨卫大电","厨房小电","生活电器","个护健康","视听影音"],
            "手机/运营商/数码"=>["手机通讯","运营商","手机配件","摄影摄像","数码配件","影音娱乐","智能设备","电子教育"],
            "电脑/办公"=>["电脑整机","电脑配件","外设产品","游戏设备","网络产品","办公设备","文具","耗材","服务产品"],
            "家居/家具/家装/厨具"=>["厨具","家纺","生活日用","家装软饰","灯具","家具","全屋定制","建筑材料","厨房卫浴","五金电工","装修设计"],
            "男装/女装/童装/内衣"=>["女装","男装","内衣","配饰","童装","童鞋"],
            "美妆/个人清洁/宠物"=>["面部护肤","香水彩妆","男士护肤","洗发护发","口腔护理","身体护理","女性护理","纸品清洗","家庭清洁","宠物生活"],
            "女鞋/箱包/钟表/珠宝"=>["时尚女鞋","潮流女包","精品男包","功能箱包","奢侈品","钟表","珠宝首饰","金银投资"],
            "男鞋/运动/户外"=>["流行男鞋","运动鞋包","运动服饰","健身训练","骑行运动","体育用品","户外鞋服","户外装备","垂钓用品","游泳用品"],
            "房产/汽车/汽车用品"=>["房产","汽车车型","汽车价格","汽车品牌","维修保养","汽车装饰","车载电器","美容清洗","安全自驾","汽车服务"],
            "母婴/玩具乐器"=>["奶粉","营养辅食","尿裤湿巾","喂养用品","洗护用品","寝居服饰","妈妈专区","童车童床","玩具","乐器"],
            "食品/酒类/生鲜/特产"=>["新鲜水果","蔬菜蛋品","精选肉类","海鲜水产","冷饮冻食","中外名酒","进口食品","休闲食品","地方特产","茗茶","饮料冲调","粮油调味"],
            "艺术/礼品鲜花/农资绿植"=>["艺术品","火机烟具","礼品","鲜花速递","绿植园艺","种子","农药","肥料","畜牧养殖","农机农具"],
            "医药保健/计生情趣"=>["中西药品","营养健康","营养成分","滋补养生","计生情趣","保健器械","护理护具","隐形眼镜","健康服务"],
            "图书/文娱/电子书"=>["文学","童书","教材教辅","人文社科","经管励志","艺术","科学技术","生活","文娱音像","教育培训","电子书","邮币"],
            "机票/酒店/旅游/生活"=>["交通出行","酒店预订","旅游度假","定制旅游","演出票务","生活缴费","生活服务","彩票","游戏"],
            "理财/众筹/白条/保险"=>["理财","众筹","东家","白条","支付","保险","股票"],
            "安装/维修/清洁/二手"=>["家电安装","办公安装","家居安装","家电维修","手机维修","办公维修","数码维修","清洗保养","特色服务","二手数码","二手电脑","二手奢品","二手书"],
            "工业品"=>["工具","劳动防护","工控配电","仪器仪表","清洁用品","化学品","安全消防","仓储包装","焊接紧固","机械配件","暖通照明","实验用品"]];
        return $catalog;
    }
    public function catalog(){
        $data=[];
        foreach (self::catalog_list() as $key=>$value){
            $this->refresh();
            $data[$key]=$this->where_in("name",$value)->select(['name','id'])->all();
        }
        return $data;
    }
    public function get_children_list(request $request){
        $id_list=$this->select(['name','id'])->where_in('name',self::catalog_list()[$request->get('name')])->get(true)->all();
        $children_list=$this->select(['name','id','parent_id'])->where_in('parent_id',array_column($id_list,'id'))->get(true)->all();
        $children_list=common::array_group_by_key($children_list,'parent_id');
        foreach ($id_list as $key=>$value){
            if(array_key_exists($value['id'],$children_list)){
                $id_list[$key]['children']=$children_list[$value['id']];
            }
        }
        return $id_list;
    }
}