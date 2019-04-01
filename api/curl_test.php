<?php
$url ="https://news.sina.cn/gn/2019-01-18/detail-ihqfskcn8357820.d.html?*cid=56261";
header("Content-type:text/html;Charset=utf-8");
$curl = curl_init();
$url = str_replace("*", "&", $url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt ($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.113 Safari/537.36");
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($curl);
curl_close($curl);
preg_match_all("/art_p\">([\s\S]*?)wx_pic/", $content, $matchs, PREG_SET_ORDER);//匹配该表所用的正则


$con = str_replace(['\n', '\t', 'art_p">', '<p class="', '</p>', '</a>', '<div id=\'wx_pic', '<a href="JavaScript:void(0)">
', '\r'], '', $matchs[0][0]);
$con = str_replace('none', 'block', $con);
$con = preg_replace("/<a href=([\s\S])*?>/", "", $con);
echo $con;