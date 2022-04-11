<?php


namespace system\cli\src;


class WorkerStartConfig
{
    /**
     * @var bool
     */
    public $autoStart;
    /**
     * @var \Closure
     */
    public $runCallBack;

    public function __construct($autoStart, $runCallBack)
    {
        $this->autoStart = $autoStart;
        $this->runCallBack = $runCallBack;
    }
}