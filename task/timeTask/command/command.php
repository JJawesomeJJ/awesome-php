<?php


namespace task\TimeTask\command;


abstract class command
{
    /**
     * 获取当前运行程序的PID
     * @return int
     */
    abstract function getMyPid():int;

    /**
     * 检查某个pid是否在运行
     * @param $pid
     * @param null $process_name
     * @return bool
     */
    abstract  function isRun($pid,$process_name=null):bool;

    /**
     * 终止某个进程
     * @param $pid
     * @param string $sign
     * @return mixed
     */
    abstract  function kill($pid,$sign="-9");

    /**
     * @param  string $php_script_path 运行的php脚本的路径和
     * @param string $php_interperter_path php解释器的路径
     * @return int 返回pid 或者false
     */
    abstract function run($php_script_path,$php_interperter_path="php");

    /**
     * 获取脚本后面的参数
     * @return mixed
     */
    abstract function getScriptParams();

    /**
     * 获取当前运行的环境
     * @return mixed
     */
    public static function getRunVersion(){
        return explode(" ",php_uname())[0];
    }
}