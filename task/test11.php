<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/16 0016
 * Time: 下午 10:07
 */
$link = mysqli_connect("localhost", "root", "zlj19971998","register");

var_dump($link->query("select * from user"));
new \db\factory\migration\migration_list\migration_survey();