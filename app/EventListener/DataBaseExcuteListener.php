<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: 2021-08-13 17:08:10
 */
namespace app\EventListener;
use app\Event\DataBaseExcuteEvent;
use system\kernel\event\EventListener;
use system\log;

class DataBaseExcuteListener extends EventListener
{
    public function handle(DataBaseExcuteEvent $event)
    {
        /**
         * @var log $log
         */
        $log = make(log::class);
        $logs =<<<logs
Sql:{$event->sql},
RunTime:{$event->runTime},
logs;
        if (!empty($event->exception)){
            $logs.=PHP_EOL.'Error:'.$event->exception->getMessage();
        }
        $log->write_log($logs);
    }
}