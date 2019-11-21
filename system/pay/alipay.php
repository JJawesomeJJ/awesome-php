<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/11 0011
 * Time: 下午 3:20
 */
namespace system\pay;
use db\db;
use load\auto_load;
use system\config\pay;

class alipay
{
    public function __construct()
    {
        auto_load::load_extend('alipay');
        echo microtime(true)-$GLOBALS['time'];
    }
    public function pay()
    {
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
        $aop->appId = pay::alipay()['appId'];
        $aop->rsaPrivateKey =pay::alipay()['rsaPrivateKey'];
        $aop->alipayrsaPublicKey=pay::alipay()['alipayrsaPublicKey'];
        $aop->apiVersion = '2.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayOpenPublicTemplateMessageIndustryModifyRequest ();
        $request->setBizContent("{" .
            "\"primary_industry_name\":\"IT科技/IT软件与服务\"," .
            "\"primary_industry_code\":\"10001/20102\"," .
            "\"secondary_industry_code\":\"10001/20102\"," .
            "\"secondary_industry_name\":\"IT科技/IT软件与服务\"" .
            "  }");
        $result = $aop->execute ($request);
        print_r($result);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            echo "成功";
        } else {
            echo "失败";
        }
    }
}