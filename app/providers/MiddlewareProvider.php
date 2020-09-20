<?php


namespace app\providers;

use http\middleware\web\RecordMiddleware;
use request\request;
use system\kernel\ServiceProvider\ServiceProvider;

class MiddlewareProvider extends ServiceProvider
{
    protected $MiddlwareGroups=[
        RecordMiddleware::class
    ];
    protected $except=[""];
    public function boot()
    {
        // TODO: Implement boot() method.
    }

    /**
     * 加载路由组
     * @param request $request
     */
    public function register(request $request)
    {
        if(!in_array($request->get_url(),$this->except)){
            foreach ($this->MiddlwareGroups as $item){
                make($item);
            }
        }
    }
}