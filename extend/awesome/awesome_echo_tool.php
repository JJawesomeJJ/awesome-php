<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/28 0028
 * Time: 上午 11:14
 */

namespace extend\awesome;


use request\request;
use system\class_define;
use system\config\channel_config;
use system\Exception;
use system\LuaScript;

class awesome_echo_tool
{
    protected $driver;
    protected $config=[
            'online'=>'awesome_echo_online',//记录当前在线的用户-record online user
            'channel'=>'awesome_echo_channel',//记录当前所有的频道-record current all channel
            'channel_suffix'=>'awesome_echo',//频道的前缀
            'user_rel_id'=>'awesome_echo_online_real',//记录当前已经验证的用户的id
            'user_token'=>'awesome_token'
    ];
    public function __construct(awesome_echo_driver $driver)
    {
        $this->driver=$driver;
    }
    /**
     * @Description 将用户的user_id与fd绑定
     * @param string $fd
     * @param string $uid
     * @param string $token 用户发送的token通过http
     * @return bool
     */
    public function bind_id(string $fd,string $uid,string $token){
        if(class_define::redis()->hGet($this->config['user_token'],$fd)==$token) {
            class_define::redis()->hSet(channel_config::awesome_echo()['user_rel_id'],$uid, $fd);
            return true;
        }else{
            return false;
        }
    }

    /**
     * @description 向用户发送一条数据
     * @param string $uid 用户的id
     * @param string $msg 消息的内容
     * @param string $handle 处理的行为应该在客户端定义
     * @param bool $is_cache 是否将数据缓存如果用户未能在线
     */
    public function send_to_user(string $uid,string $msg,string $handle='',bool $is_cache=false){
        $this->driver->send(['type'=>'send_to_user','user_id'=>[$uid],'msg'=>$msg,'handle'=>$handle,'is_cache'=>$is_cache]);
    }
    /**
     * @description 向多个用户发送一条数据
     * @param string $uid 用户的id
     * @param string $msg 消息的内容
     * @param string $handle 处理的行为应该在客户端定义
     * @param bool $is_cache 是否将数据缓存如果用户未能在线
     */
    public function send_to_users(array $uid, $msg,string $handle='',bool $is_cache=false){
        $this->driver->send(['type'=>'send_to_user','user_id'=>$uid,'msg'=>$msg,'handle'=>$handle,'is_cache'=>$is_cache]);
    }

    /**
     * @description 获取所有在线的用户
     * @return array
     */
    public function get_online_users(){
        return array_keys(class_define::redis()->hGetAll($this->config['online']));
    }

    /**
     * @description 获取已验证的在线用户
     * @return array
     */
    public function get_online_user_authenticated(){
        return class_define::redis()->hGetAll($this->config['user_rel_id']);
    }

    /**
     * @description 发送一条消息到某个频道
     * @param string $channel_name
     * @param array $msg
     * @param string $handle
     * @param bool $is_cache
     * @return mixed
     */
    public function send_to_channel(string $channel_name,array $msg,string $handle='',bool $is_cache=false){
        return $this->driver->send(['type'=>'notice_channel','channel_name'=>$channel_name,'data'=>$msg,"handle"=>$handle]);
    }
    public static function safe_check(request $request){
        $config=channel_config::awesome_echo();
        if(class_define::redis()->hGet($config['user_token'],$request->get('fd'))!=$request->get('_token')){
            new Exception('403','Forbidden');
        }
    }

    /**
     * @description 用户加入频道
     * @param string $fd
     * @param string $channel_name
     */
    public function user_add_channel(string $fd,string $channel_name){
        LuaScript::hash_add_hash($this->config['channel_suffix'].$this->config['channel'],$channel_name,$fd,"@@");
        LuaScript::hash_add_hash($this->config['online'],$fd,$channel_name,1);
    }

    /**
     * @descriptin 用户离开频道
     * @param string $fd
     * @param string $channel_name
     */
    public function user_leave_channel(string $fd,string $channel_name){
        LuaScript::hash_del_key($this->config['channel_suffix'].$this->config['channel'],$channel_name,$fd);
        LuaScript::hash_del_key($this->config['online'],$fd,$channel_name);
    }

    /**
     * @description 获取某个频道的在线人数
     * @param string $channel_name
     * @return mixed
     */
    public function channel_online_count(string $channel_name){
        return LuaScript::hash_hash_len($this->config['channel_suffix'].$this->config['channel'],$channel_name);
    }

    /**
     * @description 获取在线的人员[{id=>fd}]
     * @param array $fd_list
     * @return array
     */
    public function get_id_by_fd(array $fd_list){
        $result=class_define::redis()->hGetAll($this->config['user_rel_id']);
        return $result;
    }

    /**
     * @description 获取某个频道的在线人数以及用户的真实姓名可能为空
     * @param string $channel_name
     * @return array
     */
    public function channel_online_user(string $channel_name){
        $online_fd=class_define::redis()->hGet($this->config['channel_suffix'].$this->config['channel'],$channel_name);
        if(is_null($online_fd)||$online_fd==false){
            $online_fd='[]';
        }
        $online_fd=array_keys(json_decode($online_fd,true));
        $fd_id=array_flip($this->get_id_by_fd($online_fd));
        $result=[];
        foreach ($online_fd as $fd){
            if(array_key_exists($fd,$fd_id)){
                $result[$fd]=$online_fd[$fd];
            }else{
                $result[$fd]="";
            }
        }
        return $result;
    }
}