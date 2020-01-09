<?php
/**
 * Created by awesome.
 * Date: 2019-12-12 08:52:50
 */
namespace app\controller\language;
use app\controller\controller;
use load\auto_load;
use request\request;
use system\lock;
use translate\translate;

class language_controller extends controller
{
    public function translate(request $request){
        lock::redis_lock('language',5,10);
        auto_load::load('translate.translate');
        $translate=new translate("20191211000365022","7FNf18sNuFHbJvAJB0qg");
        lock::redis_unlock('language');
        return $translate->translate($request->get("zh"));
    }
}