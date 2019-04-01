<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/26 0026
 * Time: 上午 8:20
 */

namespace load;
use http;
use controller;

class provider
{
    protected $middleware=[
    ];
    protected $controller=[
    ];
    public function controller($controller_name){
        return new $this->controller[$controller_name];
    }
    public function middleware($middleware){
        return new $this->middleware[$middleware];
    }
}