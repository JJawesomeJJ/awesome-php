<?php


namespace app\controller\auto;


use system\route\ControllerRoute;

class module extends ControllerRoute
{
    public function controllerPath($absPath = false): string
    {
        return  "controllers";
    }
}