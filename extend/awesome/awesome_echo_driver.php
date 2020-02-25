<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/28 0028
 * Time: 上午 11:01
 */
namespace extend\awesome;
abstract class awesome_echo_driver
{
    abstract function send(array $msg);
}