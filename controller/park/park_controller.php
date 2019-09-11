<?php
/**
 * Created by awesome.
 * Date: 2019-06-27 08:05:30
 */
namespace controller\park;
use controller\auth\auth_controller;
use controller\controller;
use db\model\model_auto\model_auto;
use db\model\park\oder_park;
use request\request;
use system\common;
use system\Exception;
use system\session;

class park_controller extends controller
{
    protected $hour_per_charge=1;
    public function __construct(request $request)
    {
        parent::__construct($request);
        auth_controller::auth();
    }

    public function get_parking_status(request $request){
        $map=model_auto::model($request->get("privince"));
        if(!$map->where("id",$request->get("id"))->exist()){
            new Exception("403","data illegal");
        }
        if($this->cache()->get_cache($request->get("id"))==null){
            return false;
        }
        else{
            return true;
        }
    }
    public function start_park(request $request){
        if($this->get_parking_status($request)==false){
            $this->cache()->lock_set_cache($request->get("id"),["time"=>microtime(),"user"=>session::get("id")],"forever");
            $oder=new oder_park();
            $oder->where("user_id",session::get("id"))->where("status","unpaid")->get()->all(["id","park_id"]);
            if(!empty($oder)){
                return $oder->all_with_foreign("map","");
            }
            $id=$oder->create([
                "user_id"=>session::get("id"),
                "park_id"=>$request->get("id")
            ],true);
            return ["code"=>200,"message"=>[
                "start_at"=>microtime(),
                "oder_id"=>$id
            ]];
        }
    }
    public function stop_park(request $request){
        $charge_info=$this->get_current_money($request);
        $request->get("id");
        $oder=(new oder_park())->where("id",$request->get("id"));
        $oder->update([
            "amount_total"=>$charge_info["charge"],
            "end_at"=>$this->time(),
        ]);
        $this->cache()->delete_key($request->get("id"));
        return [
            "amount_total"=>$charge_info["charge"],
            "start_at"=>$charge_info["start_at"],
            "end_at"=>$this->time(),
            ];
    }
    public function get_current_money(request $request){
        if(($park_info=$this->cache()->get_cache($request->get("id")))!=null){
            if($park_info["user"]==session::get("id")){
                $time=microtime()-$park_info["time"];
                $money=$this->charging($time);
                return ["code"=>200,"message"=>[
                    "start_at"=>date("Y-m-d H:i:s",$time),
                    "time"=>common::get_diff_time($time,microtime()),
                    "charge"=>$this->charging($time)
                ]];
            }
        }
        new Exception(403,"data illegal");
    }
    protected function charging($sencond){
        $hour=number_format($sencond/(60*60*0.5),0);
        return $moeny=$hour*$this->hour_per_charge;
    }
    public function get_oder(request $request){
        $oder=model_auto::model("oder_park");
        if($request->try_get("status")){
            $rules=[
                "status"=>"required|accept:unpain,paid"
            ];
            $oder->where("status",$request->get("status"));
        }
        return $oder->where("user",session::get("id"))->get()->all();
    }
}