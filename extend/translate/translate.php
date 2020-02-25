<?php

/*
 * Auth jjawesome
 * Description it is a tool to help you to easily translate Chinese website to English
 * The service on baidu-translate
 * How to use it?
 * It contains two component translate.js and translate.phpchar
 * But the free baidu-translate service the QPS is 1/1 second so we have to sleep 1s per 2000
 * In summary we should cache the response！！
 * 作者-jjawesome
 * 描述-这一个基于百度翻译api的组件巴帮助你轻松的翻译你的网站这包含两个组件-前端组件-translate.js|translate.php
 * 使用：首先在后端声明一个接口然我们的翻译网站的中文内容请求到我们的服务器但是请求的次数是有限制的我们可以将请求的内容缓存起来
*/
namespace translate;


class translate
{
    protected  $app_id="";
    protected  $SEC_KEY="";
    public function __construct($app_id,$sec_key)
    {
        $this->app_id=$app_id;
        $this->SEC_KEY=$sec_key;
    }
    public  function translate($transalte_str){
        $return=[];//请求返回的值
        $salt=rand(10000,99999);
        $params_string=$transalte_str;
        foreach ($this->split_transform_text($params_string) as $item){
            if(empty($item)){
                continue;
            }
            $sign=$this->buildSign($item,$salt,$this->app_id,$this->SEC_KEY);
            $app_id=$this->app_id;
            $params=[
                "q"=>$item,
                "from"=>"zh",
                "to"=>"en",
                "appid"=>$app_id,
                "salt"=>$salt,
                "sign"=>$sign
            ];
            $result=$this->post("http://api.fanyi.baidu.com/api/trans/vip/translate",$params);
            $result=json_decode($result,true);
            if(!isset($result['trans_result'])){
                continue;
            }
            $return=array_merge($return,$result['trans_result']);
            sleep(1);//必须休眠 百度云免费的api QPS 1/1second 或者可以申请商用的套餐则无需该条
        }
        return $return;
    }
    protected function buildSign($query,$salt,$appID, $secKey){
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }

    protected function post($sUrl, $aData,$aHeader=['Content-Type:'.'application/x-www-form-urlencoded; charset=UTF-8']){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $sUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aData));
        $sResult = curl_exec($ch);
        if($sError=curl_error($ch)){
            die($sError);
            //some error happen
        }
        curl_close($ch);
        return $sResult;
    }
    protected function split_transform_text($text){
        $text_list=[];
        $item_len=1900;
        $len=ceil(mb_strlen($text)/$item_len);
        $index=0;
        for($i=0;$i<$len;$i++){
            if($i==($len-1)){
                $text_item=mb_substr($text,$index,mb_strlen($text)+1);
                $text_list[]=$text_item;
            }else {
                $text_item = mb_substr($text, $index, $item_len);
                $index = mb_strripos($text_item,"\n");
                $text_item=mb_substr($text_item,0,$index);
                $text_list[]=$text_item;
            }
        }
        return $text_list;
    }

}