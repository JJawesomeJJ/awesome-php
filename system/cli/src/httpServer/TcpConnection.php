<?php


namespace system\cli\src\httpServer;


use request\response;
use routes\routes;
use system\cli\src\eventLoop\Event;
use system\cli\src\Worker;

class TcpConnection
{
    /**
     * @var resource
     */
    public $socket;

    public function __construct($socket)
    {
        $this->socket = $socket;
        Worker::$context->eventLoop->add(new Event($socket, Event::TYPE_READ_WRITE),function ($socket){
            $this->read($socket);
        });
    }

    public function read($socket)
    {
        $buff = fread($socket, 65536);
        if (empty($buff)){
            return;
        }
        /**
         * @var response $response
         */
        $response = make(response::class);
        $response->reload();
        (new HttpPackageParse())->parseHttp($buff);
        (new routes())->start();
        $data = $response->getContent();
        fwrite($socket, $data);
    }
}