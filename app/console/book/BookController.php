<?php
namespace app\console\book;

use system\cache\cache;
use system\http;
use system\mail;

class BookController extends \app\console\ConsoleController
{
    public function index(http $http,mail $mail,cache $cache){
        $content=$http->get("http://book.zongheng.com/book/408586.html");
        $regx='<h4>最新章节<\/h4>([\s\S]*?)<a href="(.*?)"([\s\S]*?)>(.*?)<\/a>';
        preg_match_all("/$regx/u",$content,$matchs,PREG_SET_ORDER);
        print_r($matchs);
        if (!empty($matchs[0])){
            if(!empty($matchs[0][4])){
                $title=$matchs[0][4];
                $config=[
                    "1293777844@qq.com",
                    "1031492339@qq.com"
                ];
                if ($cache->get_cache(__CLASS__."TITLE")==$title){
                    return;
                }
                $cache->set_cache(__CLASS__."TITLE",$title);
                foreach ($config as $item){
                    $mail->send_email($item,"逆天邪神居然更新我的天!快去康康吧,最新章节:{$title}","小说监视者");
                }
            }
        }
    }
}