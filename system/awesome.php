<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/30 0030
 * Time: 下午 9:39
 */

namespace system;


use load\auto_load;
use load\provider;
use load\provider_register;
use system\cache\cache;
use system\config\config;

class awesome
{
    private $home_path;

    public function __construct()
    {
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

    public function load_method()
    {
        $argv = $_SERVER['argv'];
        if (count($argv) == 2) {
            $method_name = $argv[1];
            $this->$method_name();
        }
        if (count($argv) == 3) {
            $method_params = explode(":", $argv[1]);
            $method_name = $method_params[0];
            $this->$method_name($method_params[1], $argv[2]);
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
use controller;

class provider_register extends provider
{
    protected \$middleware=[
    {{middleware}}
    ];
    protected \$controller=[
     {{controller}}
    ];
    protected \$dependencies=[];
}";
        $home_path = dirname(dirname(__FILE__));
        $controller_path = "$home_path/controller/";
        $middlerware_path = "$home_path/http/middleware/";
        $file = new file();
        $middlerware_list = [];
        $controller_list = [];
        $middlerware_string = "";
        $controller_string = "";
        foreach ($file->file_walk($middlerware_path) as $value) {
            $name = explode("/", $value);
            $name = str_replace(".php", "", $name[count($name) - 1]);
            $register_value = str_replace("/", "\\", str_replace(".php", "", str_replace($home_path . "/", "", $value) . "::class"));
            if (strpos($name, "_middleware") !== false) {
                $middlerware_list[$name] = $register_value;
                $middlerware_string .= "\"$name\"=>$register_value," . "\n";
            }
        }
        foreach ($file->file_walk($controller_path) as $value) {
            $name = explode("/", $value);
            $name = str_replace(".php", "", $name[count($name) - 1]);
            $register_value = str_replace("/", "\\", str_replace(".php", "", str_replace($home_path . "/", "", $value) . "::class"));
            if (strpos($name, "_controller") !== false) {
                $controller_list[$name] = $register_value;
                $controller_string .= "\"$name\"=>$register_value," . "\n";
            }
        }
        $template_register = str_replace("{{middleware}}", $middlerware_string, str_replace("{{controller}}", $controller_string, $template_register));
        $file->write_file("$home_path/load/provider_register.php", $template_register);
    }

    public function update()
    {
        $this->register_provider();
        $this->load();
        $this->cli_echo_color_yello("provider_has_been_updated");
    }

    private function make($operate_type, $params)
    {
        switch ($operate_type) {
            case "controller":
                $this->create_controller($params);
                break;
            case "middleware":
                $this->create_middleware($params);
                break;
        }
    }

    private function create_controller($name)
    {
        $name=str_replace("\\","/",$name);
        $controller_path = $this->home_path . "controller/" . $name . ".php";
        $time = date('Y-m-d h:i:s', time());
        $arr = explode("/", $name);
        $controller_name = $arr[count($arr) - 1];
        $namespace = "controller";
        for ($i = 0; $i < count($arr) - 1; $i++) {
            $namespace .= "\\" . $arr[$i];
        }
        $controller_template = "<?php
/**
 * Created by awesome.
 * Date: $time
 */
namespace $namespace;
use controller\controller;
use request\\request;
class $controller_name extends controller
{

}";
        $file = new file();
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
        $middleware_template = "<?php
/**
 * Created by awesome.
 * Date: $time
 */
namespace $namespace;
use http\middleware;
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
            "update" => "it can update provider_register"
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
    public function update_cache_config()
    {
        $data = config::cache();
        $cache = new cache();
        if ($cache->get_cache("cache_config") == null || $cache->get_cache("cache_config") != $data) {
            $time = date("Y-m-d H:i:s");
            $cache_template = "<?php
/**
 * Created by aweomse
 * Date: $time
 */

namespace system\cache;


use system\\file;
use system\config\config;

class cache extends cache_
{
    protected \$diver={{driver}};
    protected \$path={{path}};
    //delete all cache
}";
            $cache_template = $this->replace(["driver" => $data["driver"], "path" => $data["path"]], $cache_template);
            $file = new file();
            $file->write_file($this->home_path . "system/cache/cache.php", $cache_template);
            //cache_config_has_been_change_so_update_it!
            $cache->set_cache("cache_config",$data,"2073600");
            $this->cli_echo_color_green("cache_config_has_been_updated");
        }
    }
    public function load(){
        $file=new file();
        $dependencies_string=$this->load_dependencies(config::depenendcies());
        $provider_register=$file->read_file($this->home_path."/load/provider_register.php");
        $dependencies_string=preg_replace("/protected \\\$dependencies([\s\S])*?];/",$dependencies_string,$provider_register);
        preg_match_all("/namespace load([\s\S]*?)class/",$dependencies_string, $matchs, PREG_SET_ORDER);
        $name_space_=substr($matchs[0][0],0,strlen($matchs[0][0])-5);
        $name_space=$name_space_;
        foreach (config::depenendcies() as $value){
            if(strpos($name_space,$value)==false){
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
        foreach($dependencies_path as $value) {
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
      return $this->array_to_string("dependencies",$list);
    }
    protected function replace(array $params,$template){
        foreach ($params as $key=>$value){
            $template=str_replace("{{{$key}}}","\"$value\"",$template);
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
        foreach ($file_name_list as $value){
            if(strpos($value,"migration_")!==false){
                if(count($arr)==0){
                    $class_name=str_replace(".php","",str_replace("/","\\",str_replace($this->home_path,"\\",$value)));
                    $object=new $class_name();
                    $object->create();
                    $object->create_();
                    $migration_name=explode("\\",$class_name);
                    $this->cli_echo_color_green($migration_name[count($migration_name)-1]." has been created");
                }
            }
        }
    }
}