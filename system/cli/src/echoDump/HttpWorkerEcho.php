<?php


namespace system\cli\src\echoDump;


use system\cli\src\echoDump\EventLoopEcho;

class HttpWorkerEcho
{
    public function __construct()
    {
        MasterEcho::$globalEvent = new EventLoopEcho();
        $content = stream_context_create([
            'socket' => [
                'backlog' => 1024
            ]
        ]);
        $mainSocket = stream_socket_server('tcp://0.0.0.0:9999', $errCode, $errMsg, \STREAM_SERVER_BIND |
            \STREAM_SERVER_LISTEN, $content);

//        if (\function_exists('socket_import_stream')) {
//            \set_error_handler(function(){});
//            $socket = \socket_import_stream($mainSocket);
//            \socket_set_option($socket, \SOL_SOCKET, \SO_KEEPALIVE, 1);
//            \socket_set_option($socket, \SOL_TCP, \TCP_NODELAY, 1);
//            \restore_error_handler();
//        }

        \stream_set_blocking($mainSocket, false);
        MasterEcho::$globalEvent->add($mainSocket, function ($sock) {
            $newSocket = stream_socket_accept($sock, 0, $remoteAddress);

            \stream_set_blocking($newSocket, 0);
            // Compatible with hhvm
            if (\function_exists('stream_set_read_buffer')) {
                \stream_set_read_buffer($newSocket, 0);
            }

            echo 'remote address : ' . $remoteAddress . PHP_EOL;

            echo "load" . PHP_EOL;

        }, 'stream_socket_server');

        MasterEcho::$globalEvent->loop();
    }
}