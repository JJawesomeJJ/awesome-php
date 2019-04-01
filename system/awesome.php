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

class awesome
{
    private $home_path;

    public function __construct()
    {
        if ($this->is_cli() == false) {
            echo "please operate in cli!";
            exit();
        }
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
        $controller_path = $this->home_path . "controller/" . $name . ".php";
        $time = date('Y-m-d h:i:s', time());
        $arr = explode("/", $name);
        $controller_name = $arr[count($arr) - 1];
        $namespace = "controller";
        for ($i = 0; $i < count($arr) - 1; $i++) {
            $namespace .= "/" . $arr[$i];
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
}