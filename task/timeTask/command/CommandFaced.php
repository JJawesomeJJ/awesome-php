<?php


namespace task\TimeTask\command;


class CommandFaced
{
    /**
     * @return LinuxCommand|WindowsCommad
     */
    public static function getDirver():command{
        if(command::getRunVersion()=="Linux"){
            return new LinuxCommand();
        }
        if(command::getRunVersion()=="Windows"){
            return new WindowsCommad();
        }
    }
}