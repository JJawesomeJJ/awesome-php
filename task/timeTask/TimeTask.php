<?php


namespace task\timeTask;


use request\request;
use routes\routes;
use system\cache\cache;
use system\config\config;
use system\file;
use system\log;
use task\TimeTask\command\CommandFaced;

class TimeTask
{
    protected const TIMER_TASK_KEY="TIMER_TASK_KEY";
    protected static $obj;
    protected $runTask;
    protected $cache;
    protected $consolePath;
    protected $templatePath;
    protected $file;
    protected function __construct(cache $cache){
        $this->consolePath=config::env_path()."/routes/route_console.php";
        $this->templatePath=config::env_path()."filesystem/template/";
        $this->cache=$cache;
        $this->file=new file();
        $this->runTask=[];
    }
    protected function loadRoute(){
        try {
            $this->file->mkdir($this->templatePath,0777);
            $templatePath=$this->templatePath.md5_file($this->consolePath).".php";
            if (!is_file($templatePath)){
                file_put_contents($templatePath,file_get_contents($this->consolePath));
            }
            include $templatePath;
        }
        catch (\Throwable $exception){
            $this->runTask=$this->cache->get_non_exist_set(self::TIMER_TASK_KEY,function (){
                return [];
            });
        }

    }
    public function addTask($path,$startAt,int $tick){
        if (!$this->isTime($startAt)){
            throw new \Exception("错误的日期格式:".$startAt);
        }
        $key=$path." ".$startAt.' '.$tick;
        if (!empty($this->runTask)){
            $this->runTask[$key]["tick"]=$tick;
        }
        $this->runTask[$key]=[
            "path"=>$path,
            "startAt"=>$startAt,
            "tick"=>$tick
        ];
    }
    protected function loadCache(){
        foreach ($this->runTask as $key=>$value){
            $info=$this->cache->get_cache($key.__CLASS__);
            if (!empty($info)){
                $value["startAt"]=$info["startAt"];
            }
        }
    }
    public static function SingleTon(){
        if (empty(self::$obj)){
            self::$obj=new self(make(cache::class));
        }
        return self::$obj;
    }
    public function run(){
        $log=new log();
        while (true){
            try {
                $route=new routes();
                echo date('Y-m-d H:i:s').PHP_EOL;
                $this->loadRoute();
                print_r($this->runTask);
                $this->loadCache();
                print_r($this->runTask);
                foreach ($this->runTask as $key=>&$item){
                    $startTime=$item["startAt"];
                    if (strtotime($startTime)<time()){
                        CommandFaced::getDirver()->run("awesome {$item["path"]}","php");
                        $log->write_log("执行任务===>{$item['path']}");
                        $item["startAt"]=$this->getNextTime($item["startAt"],$item["tick"]);
                        $this->cache->set_cache($key.__CLASS__,$item);
                    }
                }
                $this->runTask=[];
            }
            catch (\Throwable $exception){
                echo $exception.PHP_EOL;
                $log->write_log($exception);
            }

            sleep(10);
        }
    }
    public function getNextTime($time,int $tick){
        $timestamp=strtotime($time);
        $timestamp+=$tick;
        if ($timestamp<time()){
            return $this->getNextTime(date("Y-m-d H:i:s",$timestamp),$tick);
        }
        return date("Y-m-d H:i:s",$timestamp);
    }
    protected function isTime($time){
        return date("Y-m-d",strtotime($time))!="1970-01-01";
    }
}