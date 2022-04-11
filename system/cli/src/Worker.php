<?php


namespace system\cli\src;



use system\cli\src\common\Signal;
use system\cli\src\eventLoop\EventLoopLibEvent;

class Worker
{
    static $context;
    /**
     * @var EventLoopLibEvent
     */
    public $eventLoop;
    /**
     * @var Signal $signal
     */
    public $signal;
    /**
     * Worker constructor.
     */
    public function __construct(\Closure $callBack)
    {
        $this->eventLoop = new EventLoopLibEvent();
        self::$context = $this;
        $this->init();
        call_user_func($callBack);
    }

    protected function init()
    {
        $this->registerSignal();
    }

    protected function registerSignal()
    {
        $signal = new Signal();
        $this->signal = $signal;
        $signal->registerSignalHandler();
        $signal->registerCallBack(SIGINT,function (){
           echo '子进程接受到退出信号量' . PHP_EOL;
           exit(0);
        });
    }
}