<?php


namespace app\controller\cms;


use app\controller\controller;
use system\cache\cache;
use template\compile_parse;

class AssetsController extends controller
{
    public static function IconFont(){
        $template=view("cms/assets/icon/demo_index");
        preg_match_all('/<div class="name">(.*?)<\/div>([\s\S]*?)<div class="code-name">#(.*?)<\/div>/is',$template,$matchs);
        $result=[];
        foreach ($matchs[1] as $index=>$item){
            $result[$item]=$matchs[3][$index];
        }
        return $result;
    }
}