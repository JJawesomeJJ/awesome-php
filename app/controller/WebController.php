<?php


namespace app\controller;


use db\model\model_auto\model_auto;
use request\request;

class WebController extends controller
{
    public function create(request $request){
        return view("create",[
            'data'=>$this->getModel()->create($request->all())
        ]);
    }

    /**
     * @return model_auto|mixed
     */
    protected function getModel(){
        return model_auto::model("test");
    }
    public function index(request $request){
        return view("data",[
            "data"=>$this->getModel()->page($request->get("page"),$request->get("limit",20))
        ]);
    }
    public function view(request $request){
        return view("view",[
            'data'=>$this->getModel()->where("id",$request->get("id"))->find(1)
        ]);
    }

    /**
     * @param request $request
     * @return array|false|string
     */
    public function update(request $request){
        return view("update", [
            'data'=>$this->getModel()->where("id",$request->get("id"))->update($request->all())
        ]);
    }
}