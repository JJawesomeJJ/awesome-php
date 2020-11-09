<?php

namespace system\route;
use system\common;

abstract class ControllerRoute
{
    abstract function controllerPath($absPath=false):string;
    public function ReslovePath($controller,$method){
        $class=common::getNameSpace($this)."\\".$this->controllerPath()."\\".$controller;
        if(!class_exists($class)){
            return false;
        }else{
            $class=new \ReflectionClass($class);
            if($class->hasMethod($method)){
                return $class;
            }
            return false;
        }
    }
}