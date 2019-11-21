<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/14 0014
 * Time: 下午 10:57
 */
namespace controller\shop;
use controller\controller;

class shop extends controller
{
    public function index(){
        return view('shop/index');
    }
}