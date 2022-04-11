<?php
namespace system\cli\src\common;

class Signal
{
    protected $obj;
    public $signalCallBack = [];
    public $hasRegisterSignal = [];
    public $commonSignal = [
        SIGINT,
        SIGTERM,
        SIGHUP,
        SIGUSR1,
        SIGQUIT,
        SIGUSR2,
        SIGIO,
        SIGCHLD,
        SIGALRM
    ];
    public function signalHandler($signal)
    {
        if (array_key_exists($signal ,$this->signalCallBack)){
            call_user_func($this->signalCallBack[$signal]);
        }
        switch ($signal){
            case SIGINT:
                echo "accept quit signal" . PHP_EOL;
                exit(0);
                break;
            case SIGTERM:
                echo "accept quit signal" . PHP_EOL;
                exit(0);
            default:
                echo "accept signal " . $signal . PHP_EOL;
                break;
        }
    }
    public function registerCallBack($signal, \Closure $callBack): Signal
    {
        if (!is_array($signal)){
            $signals = [$signal];
        }else{
            $signals = $signal;
        }
        foreach ($signals as $signal){
            if (!in_array($signal, $this->hasRegisterSignal)){
                pcntl_signal($signal, function ($signal){
                    $this->signalHandler($signal);
                    $this->hasRegisterSignal[] = $signal;
                }, false);
            }
            $this->signalCallBack[$signal] = $callBack;
        }
        return $this;
    }

    public function registerSignalHandler()
    {
        $commonSignal = [
            SIGINT,
            SIGTERM,
            SIGHUP,
            SIGUSR1,
            SIGQUIT,
            SIGUSR2,
            SIGIO,
            SIGCHLD,
            SIGALRM
        ];
        pcntl_async_signals(true);
        foreach ($commonSignal as $signal){
            $this->hasRegisterSignal[] = $signal;
            pcntl_signal($signal, function ($signal){
                $this->signalHandler($signal);
            }, false);
        }
    }

    public function resetSignal()
    {
        \pcntl_signal(\SIGINT, \SIG_IGN, false);
        // uninstall stop signal handler
        \pcntl_signal(\SIGTERM, \SIG_IGN, false);
        // uninstall graceful stop signal handler
        \pcntl_signal(\SIGHUP, \SIG_IGN, false);
        // uninstall reload signal handler
        \pcntl_signal(\SIGUSR1, \SIG_IGN, false);
        // uninstall graceful reload signal handler
        \pcntl_signal(\SIGQUIT, \SIG_IGN, false);
        // uninstall status signal handler
        \pcntl_signal(\SIGUSR2, \SIG_IGN, false);
        // uninstall connections status signal handler
        \pcntl_signal(\SIGIO, \SIG_IGN, false);
    }
}