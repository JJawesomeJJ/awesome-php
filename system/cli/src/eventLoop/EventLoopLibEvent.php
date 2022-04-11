<?php


namespace system\cli\src\eventLoop;

class EventLoopLibEvent extends eventLoop
{
    public $base;

    protected $timerId = 0;
    protected $allEvent = [
        'timer' => [],
        'event' => [],
        'signal' => []
    ];

    /**
     * EventLoopLibEvent constructor.
     */
    public function __construct()
    {
        $this->base = (new \EventBase());
    }
    public function loop()
    {
        $this->base->loop();
    }

    public function addTimer($callBack,int $tick, $param = [])
    {
        $this->timerId += 1;
        $event = new \Event($this->base , -1, \Event::TIMEOUT|\Event::PERSIST, $callBack, $param);
        if ($event->addTimer($tick)){
            $this->allEvent['timer'][$this->timerId] = $event;
        }

        return $this->base;
    }
    public function add(Event $event,$callBack)
    {
        $libEvent = new \Event($this->base ,$event->fd,$event->type, $callBack);
        if ($libEvent->add()){
            $this->allEvent['event'][(int)$event->fd] = $libEvent;
        }
    }
    public function addSignal($signal, $callBack)
    {
        $event = \Event::signal($this->base, $signal, $callBack);
        if ($event->add()){
            $this->allEvent['signal'][$signal] = $event;
        }
    }
}