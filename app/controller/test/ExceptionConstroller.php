<?php


namespace app\controller\test;



use app\controller\controller;
use extend\PHPMailer\Exception;

class ExceptionConstroller extends controller
{
    public function test(){
        try {
            throw new TestException("TEST");
        }catch (TestException $exception) {
            throw $exception;
        }
        catch (\Throwable $exception){
            throw $exception;
        }
    }
}