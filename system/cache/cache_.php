<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/10 0010
 * Time: 下午 10:38
 */

namespace system\cache;

use system\kernel\facede;

class cache_ extends facede {
    public function getFacadeAccessor()
    {
        return cache::class;
    }
}

