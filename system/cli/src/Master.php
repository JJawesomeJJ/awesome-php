<?php

namespace system\cli\src;

use app\controller\test\TestException;
use PhpParser\Node\Expr\Variable;
use system\cli\src\common\Signal;

class Master
{
    protected $workerPid = [];

    /**
     * @var WorkerStartConfig[]
     */
    protected $workAndCallBack = [];

    /**
     * Master constructor.
     */
    public function __construct()
    {
        $this->registerSignal();
        echo "master process has been running pid[". posix_getppid().']'.PHP_EOL;
    }

    public function run(): void
    {
        while (true) {
            sleep(30);
        }
    }

    public function addWorkers(\Closure $callBack,bool $autoRestart = false, int $workNums = 1): Master
    {
        for ($i=0; $i<$workNums; $i++){
            $pid = $this->createWorker($callBack);
            $this->workerPid[] = $pid;
            $this->workAndCallBack[$pid] = new WorkerStartConfig($autoRestart, $callBack);
        }
        return $this;
    }

    protected function registerSignal()
    {
        $signal = new Signal();
        $signal->registerSignalHandler();
        /**
         * 主进程退出通知子进程退出
         */
        $signal->registerCallBack(SIGINT,function (){
            foreach ($this->workerPid as $value){
                posix_kill($value, SIGINT);
            }
        });
        $signal->registerCallBack(SIGCHLD, function (){
            $quitPid = pcntl_wait($status);
            foreach ($this->workerPid as $index => $pid){
                if ($pid == $quitPid){
                    unset($this->workerPid[$index]);
                    $this->workerPid = array_values($this->workerPid);
                    $this->onWorkClose($pid);
                }
            }
        });
    }

    protected function onWorkClose(int $pid): void
    {
        $workerConfig = $this->workAndCallBack[$pid];
        if ($workerConfig->autoStart){
            $this->addWorkers($workerConfig->runCallBack, true);
        }
    }

    public function createWorker(\Closure $workCallBack): int
    {
        $pid = pcntl_fork();
        if ($pid) {
            if ($pid == -1){
                return $this->createWorker($workCallBack);
            }
            return $pid;
        } else {
            new Worker($workCallBack);
            exit();
        }
    }
}