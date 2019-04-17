<?php
/**
 * Created by awesome.
 * Date: 2019-04-10 10:34:30
 */
namespace controller\index;
use controller\controller;
use request\request;
class index_controller extends controller
{
    public function index(){
        $time=microtime(true);
        for($i=0;$i<30;$i++) {
            $this->cache()->get_cacahe("file");
        }
    }
}