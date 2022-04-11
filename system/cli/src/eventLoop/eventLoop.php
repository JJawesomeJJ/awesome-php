<?php
namespace system\cli\src\eventLoop;

abstract class eventLoop
{
    abstract function add(Event $event, $callBack);
    abstract function loop();
    abstract function addSignal($signal, $callBack);
}