<?php
/**
 * Created by awesome.
 * Date: 2019-09-11 03:22:59
 */
namespace controller\pay;
use controller\controller;
use load\auto_load;
use request\request;
use system\pay\alipay;

class pay_controller extends controller
{
    public function alipay(){
        auto_load::load('alipay_test.index');
    }
}