<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/5 0005
 * Time: 下午 9:20
 */

namespace system\kernel\ServiceProvider;


abstract class ServiceProvider
{
    public function __construct()
    {
    }
    abstract function register();
    abstract function boot();
}