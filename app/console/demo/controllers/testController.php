<?php
namespace app\console\demo\controllers;

use app\console\ConsoleController;
use request\request;

class testController extends ConsoleController
{
    public function index(request $request){
        return $request->all();
    }
}