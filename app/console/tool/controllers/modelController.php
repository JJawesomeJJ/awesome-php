<?php
namespace app\console\tool\controllers;

use app\console\consoleController;
use db\factory\soft_db;
use request\request;
use system\file;

class modelController extends ConsoleController
{
    public function create(request $request){
        $table_name=$request->get("table");
        $table=soft_db::table($table_name)->getTableComment();
        print_r($this->getTemplateModel((($request->get("input",[])[2]??'')."/").$table_name,$this->compile($table)));
    }
    protected function compile($data){
        $string="";
        foreach ($data as $datum){
            $string.="          '{$datum['COLUMN_NAME']}'=>'{$datum['COLUMN_COMMENT']}',".PHP_EOL;
        }
        return substr($string,0,strlen($string)-2);
    }
    protected function getTemplateModel($name,$attribute){
        echo $name;
        $name=str_replace("\\","/",$name);
        $controller_path =DIR_PATH."db/model/".$name . ".php";
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
        /**
     * 配置字段中文名称
     * @return array
     */
    public static function attributes(){
        return [
$attribute
        ];
    }
}";
        $file = new file();
        $file->write_file($controller_path, $controller_template);
    }
}