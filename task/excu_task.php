<?php
class tets{
    public function test($is_login=false,$password="19971998"){
        echo $is_login;
    }
}
$test=new tets();
$is_login="mmd";
$test->test();