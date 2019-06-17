<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7 0007
 * Time: 下午 2:06
 */

namespace template;


use function PHPSTORM_META\type;
use system\config\config;
use system\Exception;
use system\file;

class compile
{
    protected $file;
    protected $path;
    protected $template;
    protected $data;
    protected $file_path;
    protected $http_ip;
    protected $link_file_list=[
        "script"=>[],
        "css"=>[],
    ];
    public function __construct()
    {
        $this->file = new file();
        $this->path = __DIR__;
        if($this->is_cli()){
            $this->http_ip="http://".config::server()["host_ip"];
        }else{
            $this->http_ip="http://" . $_SERVER["HTTP_HOST"];
        }
        $this->file_path = str_replace("/var/www/html", $this->http_ip, __DIR__ . "/");
    }
    public function view($template_name, array $data=[])
    {
        $this->data=$data;
        $data["path"] = str_replace("/var/www/html", $this->http_ip, __DIR__ . "/");
        $this->template= $this->file->read_file($this->path . "/" . $template_name . ".html");
        $this->compile_path($template_name);
        $this->compile_link();
        foreach ($data as $key => $value) {
            if(is_string($value)||is_numeric($value)) {
                $this->template= str_replace("{{{$key}}}", htmlentities($value), $this->template);
            }
            if(is_bool($value)){
                if($value==false) {
                    $this->template = str_replace("{{{$key}}}", "false", $this->template);
                }
                else{
                    $this->template = str_replace("{{{$key}}}", "true", $this->template);
                }
            }//debug
        }
        $this->bofore_compile_component();
        $this->compile_component();
        if ($this->template==false) {
            return new Exception("400", "template_not_exist");
        }
        foreach ($data as $key => $value) {
            if(is_string($value)||is_numeric($value)) {
                $this->template= str_replace("{{{$key}}}", htmlentities($value), $this->template);
            }
            if(is_bool($value)){
                if($value==false) {
                    $this->template = str_replace("{{{$key}}}", "false", $this->template);
                }
                else{
                    $this->template = str_replace("{{{$key}}}", "true", $this->template);
                }
            }//debug
        }
//        return $template;
        $this->compile_foreach();
        $this->compile_if();
        $this->compile_php_express();
        return $this->template;
    }
    protected function compile_foreach(){
        $data=$this->data;
        preg_match_all("/@foreach\(([\s\S]*?)\)([\s\S]*?)@endforeach/", $this->template, $matchs, PREG_SET_ORDER);//匹配该表所用的正则
        foreach ($matchs as $value_complie){
            $content="";
            eval("foreach($value_complie[1]){
            \$content.=\$value_complie[2];
            preg_match_all(\"/{{([\s\S]*?)}}/\", \$content, \$replace, PREG_SET_ORDER);
            foreach (\$replace as \$value_re){
                \$content=str_replace(\$value_re[0],eval(\"return \$value_re[1];\"),\$content);
            }
            }");
            $this->template=str_replace($value_complie[0],$content,$this->template);
        }
    }
    protected function compile_if(){
        preg_match_all("/@if\(([\s\S]*?)\)\\r([\s\S]*?)@endif/", $this->template, $matchs, PREG_SET_ORDER);
        foreach ($matchs as $value){
            if(eval("return $value[1];")){
                $value[2]=preg_split("/@(if|elseif|else)([\s\S]*?)\\r/",$value[2])[0];
                $this->template=preg_replace("/@if([\s\S]*?)@endif/",$value[2],$this->template,1);
            }
            else{
                $is_null=true;
                preg_match_all("/@elseif\(([\s\S]*?)\)\\r([\s\S]*?)@end/", $value[0], $matchs1);
                for($i=0;$i<count($matchs1[1]);$i++){
                    $code=$matchs1[1][$i];
                    if(eval("return $code;"))
                    {
                        $this->template=preg_replace("/@if([\s\S]*?)@endif/",$matchs1[2][$i],$this->template,1);
                        $is_null=false;
                        break;
                    }
                }
                if($is_null){
                    preg_match_all("/@else\r([\s\S]*?)@/", $value[0], $matchs2);
                    if(!empty($matchs2[0])) {
                        $is_null=false;
                        $this->template = preg_replace("/@if([\s\S]*?)@endif/", $matchs2[1][0], $this->template, 1);
                    }
                }
                if($is_null) {
                    $this->template = preg_replace("/@if\(([\s\S]*?)\)([\s\S]*?)@endif/", "", $this->template, 1);
                }
            }
        }
    }
    protected function compile_component(){
        $script_list="";
        $style_list="";
        preg_match_all("/@component\((.*[\s\S])\)/",$this->template,$matchs,PREG_SET_ORDER);
        while(count($matchs)>0) {
            foreach ($matchs as $value) {
                $template_component=$this->file->read_file($this->path . "/" . $value[1] . ".html");
                preg_match("/<body(.*?)>(.*[\s\S])<\/body>/is",$template_component,$template_component_mathchs);
                if($template_component==false){
                    return new Exception("400", "template_not_exist_$value[1]");
                }
                preg_match_all("/<script(.*[\s\S])<\/script>/",$template_component,$script_matchs,PREG_SET_ORDER);
                foreach ($script_matchs as $script){
                    $start=strrpos($script[1],"/");
                    if($start==false||$start<($now_start=strrpos($script[1],"\\"))){
                        $start=$now_start;
                    }
                    $start=$start+1;
                    $end=strrpos($script[1],".js");
                    $name=substr($script[1],$start,$end-$start);
                    if(!in_array($name,$this->link_file_list["script"])){
                        $script_list.="    ".$script[0]."\n";
                        $this->link_file_list["script"][]=$name;
                    }
                }
                preg_match_all("/<link(.*[\s\S])>/",$template_component,$css_matchs,PREG_SET_ORDER);
                preg_match_all("/<style(.*[\s\S])<\/style>/is",$template_component,$style_matchs,PREG_SET_ORDER);
                foreach ($css_matchs as $css){
                    $start=strrpos($css[1],"/");
                    if($start==false||$start<($now_start=strrpos($css[1],"\\"))){
                        $start=$now_start;
                    }
                    $start=$start+1;
                    $end=strrpos($css[1],".css");
                    $name=substr($css[1],$start,$end-$start);
                    if(!in_array($name,$this->link_file_list["css"])){
                        $style_list.="    ".$css[0]."\n";
                        $this->link_file_list["css"][]=$name;
                    }
                }
                foreach ($style_matchs as $style){
                    $style_list.="    ".$style[0]."\n";
                }
                preg_match("/<head>(.*[\s\S])<\/head>/is",$this->template,$head_matchs);
                $head_string=$head_matchs[1].$script_list.$style_list;
                $this->template=preg_replace("/<head>(.*[\s\S])<\/head>/is","<head>$head_string</head>",$this->template);
                $this->template = str_replace($value[0],$template_component_mathchs[2], $this->template);
                $this->compile_path($value[1]);
            }
            preg_match_all("/@extend\((.*[\s\S])\)/",$this->template,$matchs,PREG_SET_ORDER);
        }
    }
    protected function compile_path($path){
        preg_match_all("/( src| href)=\"(?!http)(?!{{)(.*?)\"/",$this->template,$url_matchs,PREG_SET_ORDER);
        $path=$this->file_path.dirname($path)."/";
        foreach ($url_matchs as $value){
            $this->template=str_replace($value[2],$path.$value[2],$this->template);
        }
    }
    protected function compile_php_express(){
        preg_match_all("/\{&([\s\S]*?)&\}/",$this->template,$express_matchs,PREG_SET_ORDER);
        foreach ($express_matchs as $express_value){
            $this->template=str_replace($express_value[0],eval("return $express_value[1];"),$this->template);
        }
    }
    protected function compile_link(){
        $script_string="";
        $style_list="";
        $template_component=$this->template;
        preg_match_all("/<script(.*[\s\S])<\/script>/",$template_component,$script_matchs,PREG_SET_ORDER);
        foreach ($script_matchs as $script){
            $start=strrpos($script[1],"/");
            if($start==false||$start<($now_start=strrpos($script[1],"\\"))){
                $start=$now_start;
            }
            $start=$start+1;
            $end=strrpos($script[1],".js");
            $name=substr($script[1],$start,$end-$start);
//            if(!isset($this->link_file_list["script"][$name])){
//                $this->link_file_list["script"][$name]=0;
//            }
            $this->link_file_list["script"][]=$name;
        }
        preg_match_all("/<link(.*[\s\S])>/",$template_component,$css_matchs,PREG_SET_ORDER);
        preg_match_all("/<style(.*[\s\S])<\/style>/is",$template_component,$style_matchs,PREG_SET_ORDER);
        foreach ($css_matchs as $css){
            $start=strrpos($css[1],"/");
            if($start==false||$start<($now_start=strrpos($css[1],"\\"))){
                $start=$now_start;
            }
            $start=$start+1;
            $end=strrpos($css[1],".css");
            $name=substr($css[1],$start,$end-$start);
//            if(!isset($this->link_file_list["css"][$name])){
//                $this->link_file_list["css"][$name]=0;
//            }
            $this->link_file_list["css"][]=$name;
        }
//        foreach ($this->link_file_list["script"] as $key=>$value){
//            if($value>1){
//                $this->template=preg_replace("/<script(.*[\s\S])$key(.*?)<\/script>/","",$this->template,$value-1);
//            }
//        }
//        foreach ($this->link_file_list["css"] as $key=>$value){
//            if($value>1){
//                $this->template=preg_replace("/<link(.*[\s\S])$key(.*?)>/","",$this->template,$value-1);
//            }
//        }
    }
    public function bofore_compile_component(){
        preg_match_all("/@compile_if\(([\s\S]*?)\)\\r([\s\S]*?)@end_compile_if/", $this->template, $matchs, PREG_SET_ORDER);
        foreach ($matchs as $value){
            if(eval("return $value[1];")){
                $value[2]=preg_split("/@(compile_if|compile_elseif|compile_else)([\s\S]*?)\\r/",$value[2])[0];
                $this->template=preg_replace("/@compile_if([\s\S]*?)@end_compile_if/",$value[2],$this->template,1);
            }
            else{
                $is_null=true;
                preg_match_all("/@compile_elseif\(([\s\S]*?)\)\\r([\s\S]*?)@end_compile_if/", $value[0], $matchs1);
                for($i=0;$i<count($matchs1[1]);$i++){
                    $code=$matchs1[1][$i];
                    if(eval("return $code;"))
                    {
                        $this->template=preg_replace("/@compile_if([\s\S]*?)@end_compile_if/",$matchs1[2][$i],$this->template,1);
                        $is_null=false;
                        break;
                    }
                }
                if($is_null){
                    preg_match_all("/@compile_else\r([\s\S]*?)@end_compile_if/", $value[0], $matchs2);
                    if(!empty($matchs2[0])) {
                        $is_null=false;
                        $this->template = preg_replace("/@compile_if([\s\S]*?)@end_compile_if/", $matchs2[1][0], $this->template, 1);
                    }
                }
                if($is_null) {
                    $this->template = preg_replace("/@compile_if\(([\s\S]*?)\)([\s\S]*?)@end_compile_if/", "", $this->template, 1);
                }
            }
        }
    }
    public static function get_tag_content($start_tag,$end_tag,$content){
        preg_match("/$start_tag(.*[\s\S])$end_tag/is",$content,$template_component_mathchs);
        return $template_component_mathchs[1];
    }
    public function is_cli()
    {
        return preg_match("/cli/i", php_sapi_name()) ? true : false;
    }
}