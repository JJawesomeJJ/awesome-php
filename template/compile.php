<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7 0007
 * Time: 下午 2:06
 */

namespace template;


use system\Exception;
use system\file;

class compile
{
    protected $file;
    protected $path;
    protected $template;
    protected $data;
    public function __construct()
    {
        $this->file = new file();
        $this->path = __DIR__;
    }

    public function view($template_name, array $data=[])
    {
        $this->data=$data;
        $data["path"] = str_replace("/var/www/html", "http://" . $_SERVER["HTTP_HOST"], __DIR__ . "/");
        $this->template= $this->file->read_file($this->path . "/" . $template_name . ".html");
        if ($this->template==false) {
            return new Exception("400", "template_not_exist");
        }
        foreach ($data as $key => $value) {
            if(is_string($value)) {
                $this->template= str_replace("{{{$key}}}", htmlentities($value), $this->template);
            }
        }
//        return $template;
        $this->compile_foreach();
        $this->compile_if();
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
            $this->template=str_replace($matchs[0],$content,$this->template);
        }
    }
    protected function compile_if(){
        preg_match_all("/@if\(([\s\S]*?)\)([\s\S]*?)@endif/", $this->template, $matchs, PREG_SET_ORDER);
        foreach ($matchs as $value){
            if(eval("return $value[1];")){
                $value[2]=preg_split("/@([if|elseif|else][\s\S]*?)\)/",$value[2])[0];
                $this->template=preg_replace("/@if([\s\S]*?)@endif/",$value[2],$this->template,1);
            }
            else{
                $is_null=true;
                preg_match_all("/@elseif\(([\s\S]*?)\)([\s\S]*?)@end/", $value[0], $matchs1);
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
                    if(!empty($matchs2)) {
                        $this->template = preg_replace("/@if([\s\S]*?)@endif/", $matchs2[1][0], $this->template, 1);
                    }
                    continue;
                }
                if($is_null) {
                    $this->template = preg_replace("/@if\(([\s\S]*?)\)([\s\S]*?)@endif/", "", $this->template, 1);
                }
            }
        }
    }
}