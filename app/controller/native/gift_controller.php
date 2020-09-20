<?php
/**
 * Created by awesome.
 * Date: 2020-02-17 02:02:27
 */
namespace app\controller\native;
use app\controller\controller;
use app\Event\native_push_event;
use db\model\native\account;
use db\model\native\gift;
use db\model\user\user;
use extend\awesome\awesome_echo_tool;
use request\request;
use system\cache\cache;
use system\class_define;
use system\common;
use system\Exception;
use system\LuaScript;
use system\session;

class gift_controller extends controller
{
    //此处配置频道礼物的列表存入redis
    protected $gift_key="native_channel_gift";
    protected $channel_like="native_channel_like";
    protected $channel_likes_count="channel_likes_count";
    public function send_gift(request $request,cache $cache,account $account){
        $rules=[
            'id'=>'reqiured',
            'channel_name'=>'reqiured',
            'num'=>'reqiured|is_number'
        ];
        $user=session::get("user")->reload();
        $gift_info=$cache->get_non_exist_set("gift-".$request->get('id'),function () use ($request){
            $gift=new gift();
            if(!$gift->where("id",$request->get("id"))->exist(false)){
                new \system\Exception(404,'GIFT NOT FIND');
            }
            return $gift->all()[0];
        });
        $gift_key=$this->gift_key;
        $user->transactions(function () use ($user,$gift_info,$account,$request,$gift_key){
            $total_amount=$gift_info['value_']*$request->get('num');
            if($user->amount<$total_amount){
                new \system\Exception(403,'Sorry balance is not enough');
            }
            $user->amount=$user->amount-$total_amount;
            $user->update();
            $id=$account->create([
                'goods_id'=>$gift_info['id'],
                'amount'=>$total_amount,
                'uid'=>$user->id,
                'num'=>$request->get('num')
            ],false,true);
            event(new native_push_event([
                'id'=>$gift_info['id'],
                'num'=>$request->get('num'),
                'gift_cover'=>$gift_info['icon'],
                'head_img'=>$user->head_img,
                'name'=>$user->name,
                'handle'=>'common',
                'action'=>'action.'.$gift_info['fun'],
                'channel_name'=>$request->get('channel_name')
            ]));
            LuaScript::hash_add_array($gift_key,$request->get("channel_name"),[
                'amount_id'=>$id,
                'num'=>$request->get('num'),
                'goods_id'=>$gift_info['id'],
                'amount_total'=>$total_amount
            ]);
        });
        return ['code'=>200,'message'=>'ok'];
    }
    public function is_like(request $request){
//        print_r($request->get("channel_name"));
//        print_r(session::get('user')->id);
        return [
            'is_like'=>LuaScript::hash_has_key($this->channel_like, $request->get("channel_name"), session::get("user")->id),
            'num'=>class_define::redis()->hGet($this->channel_likes_count,$request->get("channel_name"))
        ];
    }
    public function like(request $request){
        if(!LuaScript::hash_has_key($this->channel_like, $request->get("channel_name"), session::get("user")->id)) {
            LuaScript::hash_add_hash($this->channel_like, $request->get("channel_name"), session::get("user")->id, 1);
            return LuaScript::hash_increase($this->channel_likes_count, $request->get("channel_name"),1);
        }
        return class_define::redis()->hGet($this->channel_likes_count,$request->get("channel_name"));
    }
    public function dislike(request $request){
        if(LuaScript::hash_has_key($this->channel_like,$request->get("channel_name"),session::get("user")->id)){
            LuaScript::hash_del_key($this->channel_like,$request->get("channel_name"),session::get('user')->id);
            return LuaScript::hash_decrease($this->channel_likes_count,$request->get("channel_name"));
        }
    }
    public function get_likes(request $request){
        return class_define::redis()->hGet($this->channel_like,$request->get('channel_name'));
    }

    /**
     * 获取某个频道的的礼物数据
     * @param request $request
     * @param \Redis $redis
     * @return array
     */
    public function GetGifts(request $request,\Redis $redis){
        $result=[];
        $data=$redis->hGet($this->gift_key,$request->get("channel_name"));
        if(is_null($data)||$data==false){
            return $result;
        }else{
            $data=json_decode($data,true);
            foreach ($data as $value){
                $result[]=json_decode($value,true);
            }
            $result=common::array_group_by_key($result,'goods_id');
            return $result;
        }
    }
    public function getGiftTotal(request $request,\Redis $redis){
        $data=$redis->hGet($this->gift_key,$request->get("channel_name"));
        if(is_null($data)||$data==false){
            return 0;
        }else{
            $data=json_decode($data,true);
            foreach ($data as $value){
                $result[]=json_decode($value,true);
            }
            return array_sum(array_column($result,"amount_total"));
        }
    }
}