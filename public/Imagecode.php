<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/6
 * Time: 11:48
 */

namespace apps\defaults\controller;


class Imagecode{
    private $width ;//宽度
    private $height;//高度
    private $counts;//数量
    private $distrubcode;
    private $fonturl;//字体路径
    private $brand_name;//商标名
    private $is_mix;//是否为中文+英文
    private $english_string;//英语的字符串
    private $chinese_string;//中文的字符串
    private $up_english;//大写的字符
    private $low_english;//小写的字符
    private $e_count;
    private $e_interval;
    function __construct($brand_name,$width=120,$height=30,$fonturl="font\\FandolSong-Bold(1).otf"){
        $this->e_interval=8;
        $this->brand_name=$brand_name;//商标名
        $this->width=$width;//图片宽度
        $this->english_string=$this->has_english();//英文
        $this->chinese_string=$this->has_chinese();//中文
        $this->is_mix();//是否为单页文字
        $this->height=$height;//图片高度
        $this->counts=mb_strlen($brand_name);//商标长度
        $this->fonturl=$fonturl;//中文字体
    }
    function imageout(){
        Header("Content-type: image/GIF");
        $im=$this->createimagesource();//创建图片对象
        $this->setbackgroundcolor($im);//设置背景颜色
        $this->set_code($im);//生成文字
        ImageGIF($im);//输出
        ImageDestroy($im);//销毁对象
    }

    private function createimagesource(){
        return imagecreate($this->width,$this->height);
    }
    private function setbackgroundcolor($im){
        $bgcolor = ImageColorAllocate($im,255,255,255);
        imagefill($im,0,0,$bgcolor);
    }
    private function set_code($im){
        $english_interval_list=[
            "c"=>-5,
            "a"=>-5,
            "u"=>-4,
            "f"=>-7,
            "i"=>-9,
            "j"=>-8,
            "l"=>-8,
            "m"=>0,
            "r"=>-7,
            "s"=>-4,
            "t"=>-7,
            "w"=>0,
            "y"=>-4,
            "x"=>-4,
            "k"=>-4,
            "b"=>-4,
            "e"=>-5,
            "d"=>-4,
            "g"=>-4,
            "h"=>-5,
            "n"=>-5,
            "o"=>-4,
            "p"=>-4,
            "q"=>-3,
            "v"=>-4,
            "A"=>-3,
            "B"=>-4,
            "C"=>-3,
            "D"=>-4,
            "E"=>-4,
            "F"=>-5,
            "G"=>-3,
            "H"=>-4,
            "J"=>-5,
            "K"=>-3,
            "L"=>-6,
            "N"=>-4,
            "O"=>-3,
            "P"=>-5,
            "Q"=>-3,
            "R"=>-5,
            "X"=>-4,
            "T"=>-4,
            "U"=>-4,
            "V"=>-4,
            "W"=>2,
            "Y"=>-4,
            "Z"=>-4,
            "S"=>-5,
            "I"=>-9

        ];//英文字体间距不同字母间距不一致修改过于麻烦
        $chinese_size=30;//中文字体字体大小
        $english_size=12;//英文字体大小
        $chinese_interval=10;//中文间距
        $english_interval=0;
        $width=$this->width;
        $height=$this->height;
        $fonturl=$this->fonturl;//$this->fonturl;//中文字体
        $counts=$this->counts;
        $color = ImageColorAllocate($im, rand(0, 50), rand(50, 100), rand(100, 140));//字体颜色
        if($this->is_mix==false) {//是否为单一文字
            if(!empty($this->chinese_string)){ //只有中文
                $scode=$this->chinese_string;
                $fontsize=$chinese_size;
                $counts=mb_strlen($this->chinese_string);
                $start_y=$height/1.75;//配置垂直居中
                if($counts>3){
                    $chinese_interval=5;//设置间距
                    $fontsize=$fontsize*(1-$counts*0.06)+0.08;//文字过多缩放
                }
                $start_x=floor($width-($counts*$fontsize*1.09)-($counts-1)*$chinese_interval)/2;//设置文字起点
                for ($i = 0; $i < $counts; $i++) {
                    $char = mb_substr($scode, $i, 1);
                    if ($i != 0) {
                        $start_x = $start_x +$fontsize + $chinese_interval;
                    }
                    imagettftext($im, $fontsize, 0, $start_x, $start_y, $color, $fonturl, $char);//绘制
                }
            }
            else{
                $scode=$this->english_string;
                $english_char_list=str_split($scode);
                $lenth_list=array_intersect(array_keys($english_interval_list),$english_char_list);
                $length=0;
                foreach($lenth_list as $item){
                    $length=$length+$english_interval_list[$item];//配置间距
                }
                $fontsize=$english_size;
                $counts=mb_strlen($this->english_string);
                $start_y=$height/1.75;//配置垂直居中
                $fonturl="font\\Vision-Heavy.ttf";//设置英文字体
                $start_x=floor($width-(mb_strlen($this->up_english)*$fontsize/1.2)-(mb_strlen($this->low_english)*$fontsize/1.3)-($counts-1)*$english_interval)/2;
                //动态设置字体间距  英文比例皆为手动尝试
                for ($i = 0; $i < $counts; $i++) {
                    $char = mb_substr($scode, $i, 1);
                    if ($i != 0) {
                        $start_x = floor($start_x) + floor(13) + floor($english_interval);
                    }
                    imagettftext($im, $fontsize, 0, $start_x, $start_y, $color, $fonturl, $char);//绘制英文
                    if(in_array($char,array_keys($english_interval_list))){
                        $start_x=floor($start_x)+floor($english_interval_list[$char]);//配置间距防止英文字体层次不齐
                    }
                }
            }
        }
        else{
            //生成中文
            $scode=$this->chinese_string;
            $fontsize=$chinese_size;
            $counts=mb_strlen($this->chinese_string);
            $start_y=floor($height-(($english_interval/1.5+$chinese_interval)*1.33))/2;//配置垂直高度
            if($counts>3){
                $fontsize=$fontsize/($counts-3)*1.4;//字体缩放
                $chinese_interval=5;//间距缩放
            }//
            $start_x=floor($width-($counts*$fontsize*1.09)-($counts-1)*$chinese_interval)/2;//设置文字的开始位置
            for ($i = 0; $i < $counts; $i++) {
                $char = mb_substr($scode, $i, 1);
                if ($i != 0) {
                    $start_x = $start_x +$fontsize + $chinese_interval;
                }
                imagettftext($im, $fontsize, 0, $start_x, $start_y, $color, $fonturl, $char);
            }
            //
            //生成英文
            $scode=$this->english_string;
            $english_char_list=str_split($scode);
            $lenth_list=array_intersect(array_keys($english_interval_list),$english_char_list);
            $length=0;
            foreach($lenth_list as $item){
                $length=$length+$english_interval_list[$item];
            }//分析间距
            $fontsize=$english_size;//设置字体大小
            $counts=mb_strlen($this->english_string);
            $start_y=floor($height-(($english_interval+$chinese_interval)*1.33))/2;
            $start_y=$start_y+$chinese_size;
            $fonturl="font\\Vision-Heavy.ttf";
            $start_x=floor($width-(mb_strlen($this->up_english)*$fontsize/1.2)-(mb_strlen($this->low_english)*$fontsize/1.3)-($counts-1)*$english_interval)/2;
            for ($i = 0; $i < $counts; $i++) {
                $char = mb_substr($scode, $i, 1);
                if ($i != 0) {
                    $start_x = floor($start_x) + floor(13) + floor($english_interval);
                }
                imagettftext($im, $fontsize, 0, $start_x, $start_y, $color, $fonturl, $char);
                if(in_array($char,array_keys($english_interval_list))){
                    $start_x=floor($start_x)+floor($english_interval_list[$char]);
                }
            }
        }
    }
    protected function has_chinese(){//获取中文
        $chinese_string="";
        preg_match_all("/[\x7f-\xff]+/is",preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$this->brand_name),$matchs);
        //去除特殊字符
        $this->brand_name=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|\-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|%A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|%A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|%EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|%E2%80%98|%E2%80%99|%EF%BD%9E|%EF%BC%8E|%EF%BC%88)+/",'',$this->brand_name);
        if(isset($matchs[0])) {
            foreach ($matchs[0] as $match) {
                if($match!=' ') {
                    $chinese_string .= $match;
                }
            }
        }
        return $chinese_string;
    }
    //获取英文
    protected function has_english(){
        $english_string="";
        $hight_english="";
        $low_english="";
        $this->brand_name=trim($this->brand_name);
        preg_match_all("/[a-zA-Z]+/",$this->brand_name,$matchs);
        if(isset($matchs[0])&&!empty($matchs[0])){
            foreach($matchs[0] as $match){
                $english_string.=" ".$match;
            }
        }
        preg_match_all("/[A-Z]+/",$this->brand_name,$matchs);
        if(isset($matchs[0])&&!empty($matchs[0])){
            foreach($matchs[0] as $match){
                $hight_english.=" ".$match;
            }
        }
        preg_match_all("/[a-z]+/",$this->brand_name,$matchs);
        if(isset($matchs[0])&&!empty($matchs[0])){
            foreach($matchs[0] as $match){
                $low_english.=" ".$match;
            }
        }
        preg_match_all("/ /",trim($english_string),$matchs);
        $e_string="";
        if(isset($matchs[0])&&!empty($matchs[0])){
            foreach($matchs[0] as $match){
                $e_string.=' '.$match;
            }
        };
        $this->up_english=$hight_english;//计算大写字母的个数
        $this->low_english=$low_english;//计算小写字符长度
        $this->e_count=mb_strlen($e_string);//计算空格占得长度
        return $english_string;
    }
    //是否为混合
    protected function is_mix(){
        if(!empty($this->english_string)&&!empty($this->chinese_string)){
            $this->is_mix=true;
        }
        else{
            $this->is_mix=false;
        }
    }
}
$imagecode=new Imagecode($_GET['brand_name'],160,160);
$imagecode->imageout();