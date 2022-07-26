<?php


namespace app\providers;


use app\ServiceProvider;
use system\cache\cache;

class FacedServiceProvider extends ServiceProvider
{
    public $facedRegister = [
        cache::class => 'system\faced\Cache'
    ];
}