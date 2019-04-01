<?php
header( "Content-type:text/html;Charset=utf-8" );
$ch = curl_init();
$url ="http://news.sina.com.cn/world/";
curl_setopt ( $ch , CURLOPT_USERAGENT ,"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.113 Safari/537.36" );
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,true);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$content=curl_exec($ch);
$index=0;
$patt=substr($url,0,25)."/";
$patt=str_replace("/",'\/',$patt);
$json=array();
$reg=sprintf("/%s(.*?)shtml(.*?)<\/a>/",$patt);
preg_match_all($reg,$content,$matchs,PREG_SET_ORDER);//匹配该表所用的正则
foreach($matchs as $arr)
{
    $ar=preg_split("/\"(.*?)>/", $arr[0]);
    $data=array("url"=>$ar[0],"title"=>$ar[1]);
    $json[$index]=$data;
    $index++;
}
$new_list=json_encode(array_slice($json,1));
echo $new_list;
?>