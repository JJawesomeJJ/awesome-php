<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/25 0025
 * Time: 上午 10:52
 */

class tcp_server
{
    private $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", 6379);
        $this->start_server();
    }

    public function start_server(){
        $server = new swoole_server("127.0.0.1", 9503);
        $server->on('connect', function ($server, $fd){
            echo "connection open: {$fd}\n";
            $redis=new redis();
        });
        $server->on('receive', function ($server, $fd, $reactor_id, $data) {
            $server->send($fd, "Swoole: {$data}");
            $server->close($fd);
        });
        $server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });
        $server->start();
    }
}
new tcp_server();
