<?php


namespace system\cli\src\eventLoop;


class Event
{
    const TYPE_READ = \Event::PERSIST |\Event::READ;
    const TYPE_WRITE = \Event::WRITE |\Event::PERSIST;
    const TYPE_READ_WRITE = \Event::READ | \Event::WRITE | \Event::PERSIST;
    const TYPE_SIGNAL = 4;
    const TYPE_TIMER = 16;
    public $fd;
    public $type;
    public function __construct($fd, int $type)
    {
        $this->fd = $fd;
        $this->type = $type;
    }
}