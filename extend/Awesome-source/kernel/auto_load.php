<?php
namespace kernel;


class auto_load
{
    protected $file_Path;
    public function __construct()
    {
        $this->file_Path=APP_PATH;
        spl_autoload_register(function ($class){
            $file = str_replace('\\','/',$this->file_Path.$class . '.php');
            if (is_file($file)) {
                require_once(@$file);
                return;
            }
        });
    }
}