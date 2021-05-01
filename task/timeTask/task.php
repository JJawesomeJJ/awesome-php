<?php


namespace task\timeTask;


class task
{
    public $path;
    public function __construct($path){
        $this->path=$path;
    }
    public function tick($start_at,int $tick){
        if ($tick<1){
            throw new \Exception("无效的时间间隔");
        }
        TimeTask::SingleTon()->addTask($this->path,$start_at,$tick);
    }
}