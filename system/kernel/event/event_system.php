<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/19 0019
 * Time: 下午 10:27
 */

namespace system\kernel\event;
use system\Exception;

class event_system
{
    protected static $object=null;
    protected $event_list=[];
    public function bind($event_short_name,$event_path){
        $this->event_list[app()->get_class_path($event_short_name)]=app()->get_class_path($event_path);
    }
    public function trigger($event_name)
    {
        if ($event_name instanceof Event) {
            $this->handle_event($event_name,$this->event_list[get_class($event_name)]);
        } else {
            if(!class_exists($event_name)){
                if(($class_path=app()->get_class_path($event_name))!=null){
                    $event_name=$class_path;
//                    $event_object=make($event_name);
//                    $this->handle_event($event_object,$this->event_list[get_class($event_object)]);
                }else{
                    new Exception(404,'Event Path Not Find');
                }
            }
        }
    }
    public function handle_event(Event $event,array $listener_list){
        foreach ($listener_list as $item){
            if($item instanceof $item){
                call_user_func($item);
            }else{
                make($item)->handle($event);
            }
        }
    }
    protected function __construct()
    {
    }
    public static function SingleTon(){
        if(self::$object==null){
            self::$object=new event_system();
        }
        return self::$object;
    }
    public function bind_event(array $event_list){
        $this->event_list=$event_list;
    }
}