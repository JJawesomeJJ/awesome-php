<?php
namespace app\console\demo\controllers;

use app\console\consoleController;
use request\request;

class testController extends consoleController
{
    public function index(request $request){
        return $request->all();
    }
}