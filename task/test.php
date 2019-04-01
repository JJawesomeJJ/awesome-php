<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/21 0021
 * Time: 下午 9:12
 */

namespace task;


class test
{
    private $redis;
    public function __construct()
    {
        $locale='en_US.UTF-8';  // 或  $locale='zh_CN.UTF-8';
        setlocale(LC_ALL,$locale);
        putenv('LC_ALL='.$locale);
        $set_charset = 'export LANG=en_US.UTF-8;';
        $a = shell_exec( $set_charset."python3 /var/www/html/php/get_head_img_url.py 美女");
        echo $a;
    }
}
new test();