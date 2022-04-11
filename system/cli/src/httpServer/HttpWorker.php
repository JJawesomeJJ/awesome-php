<?php
namespace system\cli\src\httpServer;

use system\cli\src\common\Lock;
use system\cli\src\eventLoop\Event;
use system\cli\src\eventLoop\EventLoopLibEvent;
use system\cli\src\Worker;
use system\file;

class HttpWorker
{
    public static $eventLoop;
    public $socket;
    public $context;
    public function onConnect($socket){
        $newSocket = stream_socket_accept($socket);
        stream_set_blocking($newSocket,0);
        if (\function_exists('stream_set_read_buffer')) {
            \stream_set_read_buffer($newSocket, 0);
        }
        new TcpConnection($newSocket);
    }
    public function __construct(int $port)
    {
        require DIR_PATH . '/routes/' . 'route_entrance.php';
        define('AWESOME_HTTP', true);
        $signal = Worker::$context->signal;
        $signal->resetSignal();
        self::$eventLoop = Worker::$context->eventLoop;
        $this->context = stream_context_create([
            'socket' => [
                'backlog' => 102400
            ]
        ]);

        foreach ($signal->signalCallBack as $signal => $callBack){
            self::$eventLoop->addSignal($signal, $callBack);
        }
        stream_context_set_option($this->context, 'socket', 'so_reuseport', 1);
//        stream_context_set_option($this->context,'socket','so_reuseaddr',1);
        $this->socket = stream_socket_server('tcp://0.0.0.0:'.$port , $errno, $errStr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $this->context);
        if (\function_exists('socket_import_stream')) {
            \set_error_handler(function(){});
            $socket = \socket_import_stream($this->socket);
            \socket_set_option($socket, \SOL_SOCKET, \SO_KEEPALIVE, 1);
            \socket_set_option($socket, \SOL_TCP, \TCP_NODELAY, 1);
            \restore_error_handler();
        }
        stream_set_blocking($this->socket, false);
        self::$eventLoop->add(new Event($this->socket, Event::TYPE_READ), function ($socket){
            $this->onConnect($socket);
        });
        self::$eventLoop->loop();
    }
}