<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/23 0023
 * Time: 下午 9:19
 */
namespace system\kernel\Channel;
use system\config\channel_config;
use system\config\config;
use task\rabbitmq;

class Channel
{
    public function __construct(string $channel_name,string $params='')
    {
        if($channel_name!=false){
            $config=channel_config::config();
            if($config['driver']=='rabbitmq') {
                $rabbitmq=make(rabbitmq::class);
                $rabbitmq_config=channel_config::rabbitmq();
                $rabbitmq->push($rabbitmq_config['exchange'],$rabbitmq_config['queue'],json_encode(['type'=>'notice_channel','channel_name'=>$channel_name,'data'=>$params]));
            }
        }
    }
}