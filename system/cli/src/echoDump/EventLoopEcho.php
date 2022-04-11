<?php


namespace system\cli\src\echoDump;


class EventLoopEcho
{
    public $base;

    public $allEvent = [];

    public function __construct()
    {
        $this->base = new \EventBase();
    }

    public function add($fd, $cb, $type): bool
    {
        $event = new \Event($this->base, $fd, \Event::READ|\Event::PERSIST, $cb);

        $this->allEvent[(int)$fd][\Event::READ] = $event;

        echo "调用一次 $type " . (int)$fd . "\n";

        return $event->add();
    }

    public function loop(): bool
    {
        return $this->base->loop();
    }
}