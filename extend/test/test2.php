<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/2 0002
 * Time: 下午 1:53
 */

namespace extend\test;


class test2
{
    public function __construct(test1 $test1)
    {
        echo "test2";
        $test1->log();
    }
}