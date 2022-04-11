<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/30 0030
 * Time: 下午 9:39
 */
namespace system;


use app\providers\EventServiceProvider;
use app\ServiceProvider;
use db\db;
use db\factory\SqlRouter;
use load\auto_load;
use load\provider;
use load\provider_register;
use routes\routes;
use system\cache\cache;
use system\config\config;
use task\TimeTask\TimeTask;

require_once __DIR__."/../load/auto_load.php";
require_once __DIR__."/../load/common.php";
class awesome
{
    private $home_path;

    public function __construct()
    {
        config::home_path();
        if ($this->is_cli() == false) {
            $this->cli_echo_color_red("please operate in cli!");
            exit();
        }
        //require_once __DIR__."/../load/auto_load.php";
        $this->home_path = dirname(dirname(__FILE__)) . "/";
        $this->load_method();
    }

    public function is_cli()
    {
        return preg_match("/cli/i", php_sapi_name()) ? true : false;
    }
    public function timer(){
        TimeTask::SingleTon()->run();
    }
    public function load_method()
    {
        $argv = $_SERVER['argv'];
        if(count($argv)<=1){
            $this->help();
            $this->cli_echo_color_green("Route can't be resolve because of too less params input");
            return;
        }
        $method_name=$argv[1];

        $params=[];
        if(strpos($method_name,":")!==false){
            $method_name_parmas=explode(":",$method_name);
            $method_name=$method_name_parmas[0];
            $index=0;
            foreach ($method_name_parmas as $value){
                if($index==0){
                    $index++;
                    continue;
                }
                $params[]=$value;
            }
        }
        $index=0;
        foreach ($argv as $value){
            if($index<2){
                $index++;
                continue;
            }
            $params[]=$value;
        }
        if(method_exists($this,$method_name)){
            $method_obejct=new \ReflectionMethod($this,$method_name);
            $num=count($method_obejct->getParameters());
            if($num==0){
                call_user_func([$this,$method_name]);
            }
            else{
                call_user_func_array([$this,$method_name],$params);
            }
        }else{
            require_once dirname(__DIR__)."/public/index.php";
        }
    }

    public function controller($controller_name)
    {

    }

    public function middleware($middleware_name)
    {

    }

    public function register_provider()
    {
        $time = date('Y-m-d h:i:s', time());
        $template_register = "<?php
/*update_at $time;
*create_by awesome-jj
*/
namespace load;
use http;
require_once __DIR__.\"/\".\"provider.php\";
class provider_register extends provider
{
    protected static \$object;
    protected function __construct()
    {
        parent::__construct();
    }
    public static function provider(){
        if(is_null(self::\$object)){
            self::\$object=new self();
        }
        return self::\$object;
    }
    protected  \$middleware=[
{{middleware}}
    ];
    protected  \$controller=[
{{controller}}
    ];
     protected  \$console=[
{{console}}
    ];
    protected \$dependencies=[];
}";
        $home_path = dirname(dirname(__FILE__));
        $controller_path = "$home_path/".config::depenendcies()['controller'];
        $middlerware_path = "$home_path/http/middleware/";
        $console_path="$home_path/".config::depenendcies()['console'];
        $file = new file();
        $middlerware_list = [];
        $controller_list = [];
        $middlerware_string = "";
        $controller_string = "";
        $console_string="";
        $console_list=[];
        foreach ($file->file_walk($middlerware_path) as $value) {
            $name = explode("/", $value);
            $name = str_replace(".php", "", $name[count($name) - 1]);
            $register_value = str_replace("/", "\\", str_replace(".php", "", str_replace($home_path . "/", "", $value) . "::class"));
            if (strpos($name, "_middleware") !== false||strpos($name, "Middlware") !== false||strpos($name, "middlware") !== false) {
                $middlerware_list[$name] = $register_value;
                $middlerware_string .= "        "."\"$name\"=>$register_value," . "\n";
            }
        }
        foreach ($file->file_walk($controller_path) as $value) {
            $name = explode("/", $value);
            $name = str_replace(".php", "", $name[count($name) - 1]);
            $register_value = str_replace("/", "\\", str_replace(".php", "", str_replace($home_path . "/", "", $value) . "::class"));
            if (strpos($name, "_controller") !== false||strpos($name, "Controller") !== false||strpos($name, "controller") !== false) {
                $controller_list[$name] = $register_value;
                $controller_string .="        ". "\"$name\"=>$register_value," . "\n";
            }
        }
        foreach ($file->file_walk($console_path) as $value) {
            $name = explode("/", $value);
            $name = str_replace(".php", "", $name[count($name) - 1]);
            $register_value = str_replace("/", "\\", str_replace(".php", "", str_replace($home_path . "/", "", $value) . "::class"));
            if (strpos($name, "_controller") !== false||strpos($name, "Controller") !== false||strpos($name, "controller") !== false) {
                $console_list[$name] = $register_value;
                $console_string .="        ". "\"$name\"=>$register_value," . "\n";
            }
        }
        $template_register = str_replace("{{middleware}}", $middlerware_string, str_replace("{{controller}}", $controller_string, $template_register));
        $template_register=str_replace("{{console}}",$console_string,$template_register);
        $file->write_file("$home_path/load/provider_register.php", $template_register);
    }

    public function update()
    {
        $this->register_provider();
        $this->load();
        $this->cli_echo_color_yello("provider_has_been_updated");
    }

    private function make($operate_type, $params='')
    {
        switch ($operate_type) {
            case "controller":
                $this->create_controller($params);
                break;
            case "middleware":
                $this->create_middleware($params);
                break;
            case "model":
                $this->create_model($params);
                break;
            case "event":
                $this->create_event($params);
                break;
            default:
                break;
        }
    }
    public function create_service($params){

    }
    public function create_event($params){
        $listener_tpl='<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: {{time}}
 */
namespace {{namespace}}
use {{event_path}}
use system\kernel\event\EventListener;

class {{Listener}} extends EventListener
{
    public function handle({{event}} $event)
    {
        
    }
}';
        $event_tpl='<?php
/**
 * Created by awesome-cli.
 * User: Administrator
 * Date: {{time}}
 */
namespace {{namespace}}
use system\kernel\Channel\Channel;
use system\kernel\event\Event;

class {{event}} extends Event
{
    public function __construct()
    {
        parent::__construct();
    }
    /*
    @Description the event should be broadcast 
    @params Channel($channel_name,$params)
    */
     public function ShouldBroadcast()
    {
        return new Channel(false);
    }
}';
        $event_list=EventServiceProvider::$event_list;
        $env_path=config::env_path();
        foreach ($event_list as $key=>$value){
            $file_path=$env_path.'/'.$key.".php";
            $file_path = str_replace('\\','/',$file_path);
            if(!is_file($file_path)){
                if(!is_dir(dirname($file_path))){
                    mkdir(dirname($file_path));
                }
                $name_list=explode('\\',$key);
                $event_tpl_rel=$this->replace([
                    'time'=>date('Y-m-d H:m:s'),
                    'namespace'=>substr($key,0,strripos($key,'\\')?strripos($key,'\\'):strlen($key)).';',
                    'event'=>$name_list[count($name_list)-1]
                ],$event_tpl);
                file_put_contents($file_path,$event_tpl_rel);
                $this->cli_echo_color_blue("event $key has been created");
            }
            foreach ($value as $listener){
                $file_path=$env_path.'/'.$listener.".php";
                $name_list=explode('\\',$listener);
                if(!is_file($file_path)) {
                    if (!is_dir(dirname($file_path))) {
                        mkdir(dirname($file_path));
                    }
                    $event_name_list=explode('\\',$key);
                    $listener_tpl_rel=$this->replace([
                        'time'=>date('Y-m-d H:m:s'),
                        'namespace'=>substr($listener,0,strripos($listener,'\\')?strripos($listener,'\\'):strlen($listener)).';',
                        'event'=>$event_name_list[count($event_name_list)-1],
                        'Listener'=> $name_list[count($name_list)-1],
                        'event_path'=>$key.';'
                    ],$listener_tpl);
                    file_put_contents($file_path,$listener_tpl_rel);
                    $this->cli_echo_color_green("Listener $listener has been created");
                }
            }
        }
    }
    public function create_model($name){
        $name=str_replace("\\","/",$name);
        $controller_path = $this->home_path."db/model/".$name . ".php";
        $time = date('Y-m-d h:i:s', time());
        $arr = explode("/", $name);
        $controller_name = $arr[count($arr) - 1];
        $namespace = "db\model";
        for ($i = 0; $i < count($arr) - 1; $i++) {
            $namespace .= "\\" . $arr[$i];
        }
        $controller_template = "<?php
/**
 * Created by awesome.
 * Date: $time
 */
namespace $namespace;
use db\model\model;
class $controller_name extends model
{
    protected \$table_name=\"$controller_name\";
}";
        $file = new file();
        $file->write_file($controller_path, $controller_template);
        $this->update();
        $this->cli_echo_blue("model_has_been_created");
    }
    private function create_controller($name)
    {
        $name=str_replace("\\","/",$name);
        $controller_path = $this->home_path .config::depenendcies()['controller'] . $name . ".php";
        $time = date('Y-m-d h:i:s', time());
        $arr = explode("/", $name);
        $controller_name = $arr[count($arr) - 1];
        $namespace = "app\controller";
        for ($i = 0; $i < count($arr) - 1; $i++) {
            $namespace .= "\\" . $arr[$i];
        }
        $controller_template = "<?php
/**
 * Created by awesome.
 * Date: $time
 */
namespace $namespace;
use app\controller\controller;
class $controller_name extends controller
{

}";
        $file = new file();
        if(is_file($controller_path)){
            new Exception('500',"controller $name already exist!!");
        }
        $file->write_file($controller_path, $controller_template);
        $this->update();
        $this->cli_echo_blue("controller_has_been_created");
    }

    public function create_middleware($name)
    {
        $middleware_path = $this->home_path . "http/middleware/" . $name . ".php";
        $time = date('Y-m-d h:i:s', time());
        $arr = explode("/", $name);
        $middleware_name = $arr[count($arr) - 1];
        $namespace = "http\middleware";
        for ($i = 0; $i < count($arr) - 1; $i++) {
            $namespace .= "/" . $arr[$i];
        }
        $namespace=str_replace("/","\\",$namespace);
        $middleware_template = "<?php
/**
 * Created by awesome.
 * Date: $time
 */
namespace $namespace;
use http\middleware\middleware;
class $middleware_name extends middleware
{
     public function check()
     {
        // TODO: middleware 入口.
     }
}";
        $file = new file();
        $file->write_file($middleware_path, $middleware_template);
        $this->update();
        $this->cli_echo_blue("middleware_has_been_created");
    }

    public function help()
    {
        $this->cli_echo_red("this is framework tool can help you to fast use it --design by jjawesome use as awesome command");
        $help_list = [
            "make:controller controller_name" => "it can create a controller to control your work at the same time tool will update provider_register",
            "make:middleware middleware_name" => "it can create a middleware at same time tool will update provider_register,it can hlep you to filter danger_input",
            "update" => "it can update provider_register",
            "migrate"=>"to do migation file"
        ];
        foreach ($help_list as $key=>$value)
        {
            $this->cli_echo_blue($key);
            $this->cli_echo_green($value);
        }
    }

    public function cli_echo_blue($messgae)
    {
        $command = "echo  \"\033[44;37m $messgae \033[0m\" " . PHP_EOL;
        system("$command");
    }

    public function cli_echo_red($message)
    {
        $command = "echo  \"\033[41;37m $message \033[0m\" ";
        system($command);
    }

    public function cli_echo_green($message)
    {
        $command = "echo \"\033[42;37m $message \033[0m\" ";
        system($command);
    }
    public function cli_echo_color_green($message){
        $command=" echo  \"\033[32m$message \033[0m\"";
        system($command);
    }
    public function cli_echo_color_blue($message){
        $command=" echo  \"\033[34m$message \033[0m\"";
        system($command);
    }
    public function cli_echo_color_red($message){
        $command=" echo  \"\033[31m$message \033[0m\"";
        system($command);
    }
    public function cli_echo_color_yello($message){
        $command=" echo  \"\033[33m$message \033[0m\"";
        system($command);
    }
    public function update_config()
    {
        $cache = new cache();
        $config_cache = null;
        $md5_key = md5_file($this->home_path . "system/config/config.php");
        if($cache->get_cache("config")==null||$cache->get_cache("config")!=$md5_key){
            $cache->set_cache("config",$md5_key,2073600);
            $this->update_cache_config();
        }
    }
//    public function update_cache_config()
//    {
//        $data = config::cache();
//        $cache = new cache();
//        if ($cache->get_cache("cache_config") == null || $cache->get_cache("cache_config") != $data) {
//            $time = date("Y-m-d H:i:s");
//            $cache_template = "<?php
///**
// * Created by aweomse
// * Date: $time
// */
//
//namespace system\cache;
//
//
//use system\\file;
//use system\config\config;
//
//class cache extends cache
//{
//    protected \$diver={{driver}};
//    protected \$path={{path}};
//    //delete all cache
//}";
//            $cache_template = $this->replace(["driver" => $data["driver"], "path" => $data["path"]], $cache_template);
//            $file = new file();
//            $file->write_file($this->home_path . "system/cache/cache.php", $cache_template);
//            //cache_config_has_been_change_so_update_it!
//            $cache->set_cache("cache_config",$data,"2073600");
//            $this->cli_echo_color_green("cache_config_has_been_updated");
//        }
//    }
    public function load(){
        $file=new file();
        $dependencies_string=$this->load_dependencies(config::depenendcies());
        $provider_register=$file->read_file($this->home_path."/load/provider_register.php");
        $dependencies_string=preg_replace("/protected \\\$dependencies([\s\S]*?)];/",$dependencies_string,$provider_register);
        preg_match_all("/namespace load([\s\S]*?)class/",$dependencies_string, $matchs, PREG_SET_ORDER);
        $name_space_=substr($matchs[0][0],0,strlen($matchs[0][0])-5);
        $name_space=$name_space_;
        foreach (config::depenendcies()['must'] as $value){
            if(strpos($name_space,$value)==false){
                $value=str_replace("/","\\",$value);
                if($value[strlen($value)-1]=="\\"){
                    $value=substr($value,0,strlen($value)-1);
                }
                $value=explode('\\',$value)[0];
                $name_space.="use $value;\n";
            }
        }
        $time = date("Y-m-d H:i:s");
        $dependencies_string=str_replace($name_space_,$name_space,$dependencies_string);
        $file->write_file($this->home_path."/load/provider_register.php",preg_replace("/\\*update_at.*?;/","*update_at $time",$dependencies_string));
    }
    public function load_dependencies(array $dependencies_path){
        $list=[];
        $file=new file();
        foreach($dependencies_path['must'] as $value) {
            foreach ($file->file_walk($this->home_path ."$value/") as $value) {
                if (substr($value, -3) == "php") {
                    $dependency_class_name = str_replace(".php","",substr($value, strrpos($value, "/") + 1, strlen($value) - strrpos($value, "/")));
                    $class_path = str_replace($this->home_path, "", $value);
                    $class_path=str_replace("/","\\",$class_path);
                    $class_path=str_replace(".php","",$class_path);
                    $list[$dependency_class_name] = $class_path;
                }
            }
        }
        $file=new file();
        $class_path_dir=$this->home_path."/filesystem/class_path";
        if(!is_dir($class_path_dir)){
            mkdir($class_path_dir);
        }
        foreach ($dependencies_path['extend'] as $key=>$value){
            $extend_list=[];
            foreach ($file->file_walk($this->home_path ."$value/") as $value) {
                if (substr($value, -3) == "php") {
                    $dependency_class_name = str_replace(".php","",substr($value, strrpos($value, "/") + 1, strlen($value) - strrpos($value, "/")));
                    $class_path = str_replace($this->home_path, "", $value);
                    $class_path=str_replace("/","\\",$class_path);
                    $class_path=str_replace(".php","",$class_path);
                    $extend_list[$dependency_class_name] = $class_path;
                }
            }
            $file->write_file($class_path_dir."/".$key.".txt",json_encode($extend_list));
        }
      return $this->array_to_string("dependencies",$list);
    }
    protected function replace(array $params,$template){
        foreach ($params as $key=>$value){
            $template=str_replace("{{{$key}}}",$value,$template);
        }
        return $template;
    }
    public function array_to_string($arr_name,$arr){
        $arr_string="";
        foreach ($arr as $key=>$value){
            $arr_string.="        \"$key\"=>$value::class,". "\n";
        }
        $arr_name=sprintf("protected \$$arr_name=[\n%s];",$arr_string);
        return $arr_name;
    }
    public function make_new_queue($queue_name){

    }
    public function migrate(...$arr){
        $file=new file();
        $file_name_list=$file->file_walk($this->home_path."db/");
        $class_name_list=[];
        foreach ($file_name_list as $value) {
            if (strpos($value, "migration_") !== false) {
                $class_name_list[] = str_replace(".php", "", str_replace("/", "\\", str_replace($this->home_path, "\\", $value)));
            }
        }
        print_r($class_name_list);
        if(isset($arr[1])){
            $class_name_list=[$arr[1]];
        }
        if(isset($arr[0])){
            $type=$arr[0];
        }
        else{
            $type="create";
        }
        switch ($type){
            case "create":
                foreach ($class_name_list as $class_name){
                    $object=make($class_name);
                    $result=$object->create_();
                    foreach ($result as $key=>$value){
                        if($value){
                            $this->cli_echo_color_green($key. " has been suceess create!");
                        }
                    }
                }
                break;
            case "update":
                foreach ($class_name_list as $class_name){
                    $object=make($class_name);
                    $result=$object->update();
                    foreach ($result as $key=>$value){
                        if($value){
                            $this->cli_echo_color_green($key. " has been update!");
                        }
                    }
                }
                break;
            case "drop":
                foreach ($class_name_list as $class_name){
                    $object=make($class_name);
                    $result=$object->drop();
                    foreach ($result as $key=>$value){
                        if($value){
                            $this->cli_echo_color_red($key. " has been delete!");
                        }
                    }
                }
                break;
            case "refresh":
                foreach ($class_name_list as $class_name){
                    $object=make($class_name);
                    $object->create();
                    $result=$object->refresh();
                    foreach ($result as $key=>$value){
                        if($value){
                            $this->cli_echo_color_blue($key. "data has been refreshed!");
                        }
                    }
                }
                break;
            default:
                break;
        }
//        foreach ($file_name_list as $value){
//            if(strpos($value,"migration_")!==false){
//                if(count($arr)==0){
//                    $class_name=str_replace(".php","",str_replace("/","\\",str_replace($this->home_path,"\\",$value)));
//                    $object=new $class_name();
//
//                    }
//                else{
//                    if($arr[0]=="update"){
//                        $class_name=str_replace(".php","",str_replace("/","\\",str_replace($this->home_path,"\\",$value)));
//                        $object=new $class_name();
//                        $object->create();
//                        if($object->update()) {
//                            $migration_name = explode("\\", $class_name);
//                            $this->cli_echo_color_green($migration_name[count($migration_name) - 1] . " has been update");
//                        }
//                    }
//                }
//            }
//        }
    }
    public function flushsql(){
        $keys=[
            SqlRouter::$sql_router_key,
            SqlRouter::$fail_node
        ];
        foreach ($keys as $key){
            class_define::redis()->del($key);
        }
        $this->cli_echo_color_green("Sql Router Cache has been flushed!!");
    }
    public function task(...$arr){
        switch ($arr[0]){
            case "show":
                foreach (\system\config\queue::queue_handle() as $key=>$value){
                    if(system_excu::get_process_status($key)){
                        $this->cli_echo_color_green("process $key is already running!");
                    }
                    else{
                        $this->cli_echo_color_red("process $key is not start!!");
                    }
                }
                break;
            case "start":
                $task_list=array_keys(\system\config\queue::queue_handle());
                if(isset($arr[1])){
                    $task_list=[$arr[1]];
                }
                foreach ($task_list as $item) {
                    if (system_excu::get_process_status($item)) {
                        $this->cli_echo_color_yello("$item is alreay running!");
                    } else {
                        if (!is_file(\system\config\queue::queue_handle()[$item])) {
                            new Exception('403', "$item is not a file");
                        }
//                        echo \system\config\queue::queue_handle()[$item];
                        echo $item.PHP_EOL;
                        system_excu::excu_asyn(\system\config\queue::queue_handle()[$item]);
                    }
                }
                break;
            case "stop":
                $task_list=array_keys(\system\config\queue::queue_handle());
                if(isset($arr[1])){
                    $task_list=[$arr[1]];
                }
                $signal="-15";
                if(isset($arr[2])){
                    $signal=$arr[2];
                }
                foreach ($task_list as $item) {
                    if(system_excu::kill_task($item,$signal)) {
                        $this->cli_echo_color_blue("process $item has been killed");
                    }
                }
                break;
            default:
                break;
        }
    }
}