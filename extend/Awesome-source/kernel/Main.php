<?php

define("APP_PATH",dirname(__DIR__)."/");
define("SATRT_AT",microtime(true));
class Main
{
    public function __construct()
    {
        require_once APP_PATH."kernel/auto_load.php";
    }
    public function start(){
        Co\run(function () {
            $server = new Co\Http\Server("127.0.0.1", 9502, false);
            $server->handle('/', function ($request, $response) {
                $response->end("<h1>Index</h1>");
            });
            $server->handle('/test', function ($request, $response) {
                $response->end("<h1>Test</h1>");
            });
            $server->handle('/stop', function ($request, $response) use ($server) {
                $response->end("<h1>Stop</h1>");
                $server->shutdown();
            });
            $server->start();
        });
    }
}