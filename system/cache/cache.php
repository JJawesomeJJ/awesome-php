<?php
/**
 * Created by aweomse
 * Date: 2019-04-11 21:10:43
 */

namespace system\cache;


use system\file;
use system\config\config;

class cache extends cache_
{
    protected $diver="redis";
    protected $path="filesystem";
    //delete all cache
}