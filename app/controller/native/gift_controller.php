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
use request\request;
use system\cache\cache;
use system\Exception;
use system\LuaScript;
use system\session;

class gift_controller extends controller
{
    //此处配置频道礼物的列表存入redis
    protected $gift_key="native_channel_gift";
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
}