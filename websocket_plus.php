<?php
require_once ("load/auto_load.php");
$server=new server();
global $time;
class server
{
    private $addr = "0.0.0.0";
    //监听地址本机
    private $port = "9502";
    //监听端口
    public $time = 0;
    public $redis;
    public $name;
    private $error_file_path;
    public $users = array();
    //上线用户的数量
    public function __construct()
    {
        $this->error_file_path = dirname(__FILE__) . "/error.txt";
        //错误日志地址
        $this->lock = new \swoole_lock(SWOOLE_MUTEX);
        $this->server = new \swoole_websocket_server($this->addr, $this->port);
        $this->server->set(array(
            'daemonize' => 1,
            'worker_num' => 4,
            'task_worker_num' => 10,
            'max_request' => 1000,
            'log_file' => dirname(__FILE__) . "/error.txt"
        ));
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", 6379);
        $this->redis->del("users");
        $this->redis->del("user_list");
        $this->server->on('open', array($this, 'onopen'));//
        $this->server->on('message', array($this, 'onmessage'));
        $this->server->on('task', array($this, 'onTask'));
        $this->server->on('finish', array($this, 'onfinish'));
        $this->server->on('close', array($this, 'onclose'));
        $this->server->on('start',array($this,'onstart'));
        $this->server->start();
    }
    public function onstart(){
        \system\system_excu::record_my_pid(__FILE__);
    }
    public function onopen($server, $request)
    {
        $this->time = $this->time + 1;
        $message = array(
            'remote_addr' => $request->server['remote_addr'],
            'request_time' => date('Y-m-d H:i:s', $request->server['request_time'])
        );
        echo "add";
    }
    public function onmessage($server, $frame)
    {
        $data = json_decode($frame->data);
        switch ($data->type) {
            case 'init':
                //$this->users[$data->username]=$frame->fd;
                if (!$this->vertify_user($server, $frame, $data)) {
                    return;
                }
                echo $data->username . "add_con" . PHP_EOL;
                $this->query_wait_handle_task($data->username);
                $user = $this->get_user();
                $count = count($user);
                echo $count . PHP_EOL;
                $response = array(
                    'type' => 'system',
                    'online' => $user,
                    'count' => $count,
                    'online_count' => $count,
                    'time' => $this->time
                );
                echo json_encode($response) . PHP_EOL;
                //$this->server->push($frame->fd,json_encode($response));
                $this->server->task($response);
                break;
            case 'chat':
                $source = $data->source;
                if ($source == "system") {
                    if ($data->check_code == "13036591969") {
                        if ($data->message == "handle_task") {
                            try {
                                $this->handle_notify();
                            } catch (\Exception $exception) {
                                $this->write_error($exception);
                            }
                            return;//处理队列中的人物
                        }
                        echo "load";
                        $response = $data->message;
                        $this->server->task($response);
                    } else {
                        $this->server->close($frame->fd);//非法用户关闭它
                    }
                    return;
                }
                if (!$this->check_user($data->source, $frame, $server)) {
                    return false;
                } else {
                    if ($data->message_type == "image") {
                        $to = $data->to;
                        $response = array(
                            "source" => $data->source,
                            "message" => $data->message,
                            "type" => "image"
                        );
                        $user = $this->get_user();
                        $this->server->push($user[$to], json_encode($response));
                    } else {
                        $to = $data->to;
                        $response = array(
                            "source" => $data->source,
                            "message" => $data->message,
                            "type" => 'text'
                        );
                        $user = $this->get_user();
                        $this->server->push($user[$to], json_encode($response));
                    }
                }
                break;
            default:
                $this->server->close($frame->fd);
                return false;
        }
    }
    public function onTask($server, $task_id, $form_id, $message)
    {
        $server->finish('Task' . $task_id . 'Finished' . PHP_EOL);
        $user_list = $this->get_user();
        foreach ($user_list as $key => $value) {
            $this->server->push($value, json_encode($message));
        }
    }
    public function onclose($server, $fd)
    {
        $this->lock->lock();
        $this->delete_user($this->name);
        echo $this->name . 'close' . PHP_EOL;
        $user = $this->get_user();
        $response = array(
            'type' => 'system',
            'online' => $user,
            'count' => count($user),
            'online_count' => count($user),
            'time' => $this->time
        );
        //$this->server->push($frame->fd,json_encode($response));
        if($this->name!=""&&$this->name!=null) {
            $this->server->task($response);
        }
        $this->lock->unlock();
    }
    public function onfinish()
    {
    }
    public function vertify_user($server, $frame, $data)
    {
        if (!$this->redis->hExists("users", $data->username)) {
            $server->push($frame->fd,json_encode(["type"=>"close","message"=>"认证失败"]));
            $this->server->close($frame->fd);//鉴权失败非法用户禁止连接
            echo "鉴权失败$data->username" . PHP_EOL;
            return false;
        }
        $user_data = json_decode($this->redis->hGet("users", $data->username), true);
        if ($user_data["token_value"] != $data->token_value) {
            $server->push($frame->fd,json_encode(["type"=>"close","message"=>"登录失效"]));
            $server->close($frame->fd);
            return false;
        }
        $this->store_user($server, $frame, $data->username);
        return true;
    }
    public function store_user($server, $frame, $user_name)
    {
        $this->name = $user_name;
        if ($this->redis->hExists("user_list", $user_name)) {
            $server->push($this->redis->hGet("user_list",$user_name),json_encode(["type"=>"close","message"=>"异地登录"]));
            try {
                $server->close($this->redis->hGet("user_list", $user_name));
            }
            catch (Exception $exception)
            {
                $this->write_error($exception);
            }
            echo $user_name . "异地登录" . PHP_EOL;
        }//判断有没有相同用户名的用户登录，如果有即判断为异地登录
        $this->redis->hSet("user_list", $user_name, $frame->fd);
    }
    public function delete_user($user_name)
    {
        $this->redis->hDel("user_list", $user_name);
    }
    public function get_user()
    {
        $user_list = $this->redis->hGetAll("user_list");
        return $user_list;
    }
    public function check_user($user_name, $frame, $server)
    {
        if ($this->redis->hGet("user_list", $user_name) == $frame->fd) {
            return true;
        } else {
            $server->close($frame->fd);//用户非法关闭连接
            echo "非法用户$user_name" . PHP_EOL;
            return false;
        }
    }
    public function handle_notify()
    {
        while ($this->redis->lLen("notify_list") > 0) {
            $data = json_decode($this->redis->lPop("notify_list"), true);
            $method_name = $data["handle_type"];
            try {
                $this->$method_name($data);
            } catch (\Exception $exception) {
                $this->redis->lPush("fail_hanle_list", json_encode($data));
                $this->write_error($exception);
            }
        }
    }//when rouser tell server the to handle task this method response it!
    public function notify_user($data)
    {
        $data = $data["handle_params"];
        if ($this->redis->hExists("user_list", $data["user_name"])) {
            $this->server->push($this->redis->hGet("user_list", $data["user_name"]), json_encode($data["message"]));
        }//the user online handle at once
        else {
            if ($this->redis->hExists("wait_notify_list", $data["user_name"])) {
                $hanle_params = json_decode($this->redis->hGet("wait_notify_list", $data["user_name"]), true);
                $hanle_params[] = $data["message"];
                $this->redis->hSet("wait_notify_list", $data["user_name"], json_encode($hanle_params));
            }//the wait_handle_list has the message of this user add new message
            else {
                $this->redis->hSet("wait_notify_list", $data["user_name"], json_encode([$data["message"]]));
            }//the wait_hanle_list not_exists_key create
        }
    }
    public function write_error($error_message)
    {
        $fd = fopen($this->error_file_path, "a");
        fwrite($fd, $error_message . date('Y-m-d H:i:s', time()) . "\n");
        fclose($fd);
    }//write_error;
    public function query_wait_handle_task($user_name)
    {
        if($this->redis->hExists("wait_notify_list",$user_name))
        {
            $this->server->push($this->redis->hGet("user_list",$user_name),$this->redis->hGet("wait_notify_list",$user_name));
            $this->redis->hDel("wait_notify_list",$user_name);
        }
    }//when user login server,we will check the wait_notify_list whether hava this user untreated task;
}
?>