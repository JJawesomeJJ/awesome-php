<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/29 0029
 * Time: 下午 11:12
 */

namespace template;
use system\common;
use system\config\config;
use system\Exception;
use system\file;

class compile_parse
{
    protected $cache_template_path="filesystem/template";//编译好的template文件缓存路径
    protected $template="";
    protected $link_file_list=[
        'css'=>[],
        'script'=>[]
    ];
    protected $mata=[];//所有页面的meta
    protected $file_path=null;
    protected $http_ip;
    protected $cache;
    protected $file;//文本对象
    protected $template_link_info=[];//关联模板信息
    protected static $object;
    public function __construct()
    {
        $this->file=new file();
        $this->cache=make('cache');
        $this->http_ip=config::request_path();
    }
    public static function compile($template_path,array $data=[],$is_single=true){
        if($is_single=true){
            self::$object=new compile_parse();
        }else {
            if (self::$object == null) {
                self::$object = new compile_parse();
            }
        }
        $file_name=config::env_path().'/public/template/'.$template_path.".html";
        $cache_file_path=config::env_path()."/".self::$object->cache_template_path."/".md5(self::$object->http_ip).md5_file($file_name).".php";
        if(self::$object->is_restart_compile($template_path)||!is_file($cache_file_path)){
            $cache_file_path=self::$object->compile_template($template_path);
        }
        $data['request']=make('request');
        foreach ($data as $key=>$value){
            $$key=$value;
        }
        ob_start();
        include $cache_file_path;
        $data=ob_get_contents();
        ob_clean();
        return $data;
    }
    protected function is_restart_compile($template_path){
        if(($cache_info=$this->cache->get_cache($template_path))==null){
            return true;
        }
        else{
            foreach ($cache_info as $key=>$value){
                if(!is_file($key)){
                    return true;
                }
                if(md5_file($key)!=$value){
                    return true;
                }
            }
        }
        return false;
    }
    public function start_compile($template_path){
        $this->file_path = str_replace('\\','/',str_replace(config::env_path().'/public', $this->http_ip, __DIR__ . "/"));
        $template_path_abs=config::env_path()."/public/template/$template_path.html";
        if(!is_file($template_path_abs)){
            new Exception("400",$template_path_abs." NOT FIND");
        }
        $this->template_link_info[$template_path_abs]=md5_file($template_path_abs);
        $template_data=$this->file->read_file($template_path_abs);
        $template_data=$this->compile_path($template_path,$template_data);
        $this->compile_link($template_data);
        $template_data=$this->compile_component($template_data);
        $template_data=$this->compile_extend($template_data);
        $this->compile_meta($template_data);
        return $template_data;
    }
    protected function compile_template($template_path){
        $template_data=$this->start_compile($template_path);
        $template_data=$this->compile_header($template_data);
        $template_data=$this->parse($template_data);
        $template_path_abs=config::env_path()."/public/template/$template_path".".html";
        $file_name=md5($this->http_ip).md5_file($template_path_abs);
        $cache_file_name=config::env_path()."/$this->cache_template_path/$file_name.php";
        $this->file->write_file($cache_file_name,$template_data);
        $this->cache->set_cache($template_path,$this->template_link_info,"forever");
        return $cache_file_name;
    }
    public function get_data(){
        return $this->template;
    }

    /**
     * @description 将各种表达式转化为php可以识别的php代码
     * @param $template_str
     * @return string|string[]|null
     */
    protected function parse($template_str){
        $template_str=preg_replace("/@if\(([\s\S]*?)\)\\r/","<?php if(\\1):?>",$template_str);
        $template_str =preg_replace("/@else/is", " <?php else: ?>",$template_str);
        $template_str=preg_replace("/@endif/","<?php endif; ?>",$template_str);
        $template_str=preg_replace("/@elseif\(([\s\S]*?)\)\\r/","<?php elseif(\\1): ?>",$template_str);
        $template_str=preg_replace("/@{{(.*?)}}/","@@\\1@@",$template_str);
        $template_str=preg_replace("/{{\\$(.*?)}}/","<?php echo \$\\1; ?>",$template_str);
        $template_str=preg_replace("/{{(.*?)}}/","<?php echo \\1 ?>",$template_str);
        $template_str=preg_replace("/@foreach\(([\s\S]*?)\)\\r/","<?php foreach(\\1): ?>",$template_str);
        $template_str=preg_replace("/@endforeach\\r/","<?php endforeach; ?>",$template_str);
        $template_str=preg_replace("/@for\(([\s\S]*?)\)\\r/","<?php for(\\1): ?>",$template_str);
        $template_str=preg_replace("/@endfor\\r/","<?php endfor; ?>",$template_str);
        $template_str=preg_replace("/@switch\((.*?)\)(.*?)@case\((.*?)\)/is"," <?php switch(\\1):".PHP_EOL. "case (\\3): ?>",$template_str);
        $template_str=preg_replace('/@break/','<?php break;?>',$template_str);
        $template_str=preg_replace('/@case\((.*?)\)/is',"<?php case(\\1):?>",$template_str);
        $template_str=preg_replace("/@default/is",'<?php default: ?>',$template_str);
        $template_str=preg_replace("/@endswitch/is"," <?php endswitch; ?>",$template_str);
        $template_str=preg_replace("/{&(.*?)&}/","<?php \\1; ?>",$template_str);
        $template_str=preg_replace("/@@(.*?)@@/","{{\\1}}",$template_str);
        return $template_str;
    }

    /**
     * @description 将数据写入header
     * @param $template_data
     * @return string|string[]|null
     */
    protected function compile_header($template_data){
        $template_data=preg_replace("/<script(.*[\s\S])<\/script>/","",$template_data);
        $template_data=preg_replace("/<link(.*[\s\S])>/","",$template_data);
        preg_match("/<head>(.*[\s\S])<\/head>/is",$template_data,$head_matchs);
        $head_string=preg_split('/\\n/',$head_matchs[1]);
        $new_string="";
        foreach ($head_string as $value){
            if(!empty(trim($value))) {
                $new_string.= $value.PHP_EOL;
            }
        }
        foreach ($this->link_file_list["script"] as $js){
            $new_string.="    ".$js.PHP_EOL;
        }
        foreach ($this->link_file_list["css"] as $css){
            $new_string.="    ".$css.PHP_EOL;
        }
        $meta_string="";
        foreach (array_unique($this->mata) as $item){
            $meta_string.=$item.PHP_EOL;
        }
        $new_string=$meta_string.$new_string;
        $template_data=preg_replace("/<head>(.*[\s\S])<\/head>/is",'<head>'.PHP_EOL.$new_string.'</head>',$template_data);
        return $template_data;
    }

    /**
     * @description 编译每个页面的css与js
     * @param $template_data
     */
    protected function compile_link($template_data){
        $script_string="";
        $style_list="";
        $template_component=$template_data;
        preg_match_all("/<script(.*[\s\S])<\/script>/",$template_component,$script_matchs,PREG_SET_ORDER);
        foreach ($script_matchs as $script){
            $start=strrpos($script[1],"/");
            if($start==false&&$start<($now_start=strrpos($script[1],"\\"))){
                $start=$now_start;
            }
            $start=$start+1;
            $end=strrpos($script[1],".js");
            $name=substr($script[1],$start,$end-$start);
            $this->link_file_list["script"][$name]=$script[0];
        }
        preg_match_all("/<link(.*[\s\S]).css(.*?)>/",$template_component,$css_matchs,PREG_SET_ORDER);
        preg_match_all("/<style(.*[\s\S])<\/style>/is",$template_component,$style_matchs,PREG_SET_ORDER);
        foreach ($css_matchs as $css){
            $start=strrpos($css[1],"/");
            if($start==false&&$start<($now_start=strrpos($css[1],"\\"))){
                $start=$now_start;
            }
            $name=substr($css[1],$start+1,strlen($css[1])-$start);
            $this->link_file_list["css"][$name]=$css[0];
        }
    }

    /**
     * @description 获取每个组件的里面href
     * @param $path
     * @param $template_data
     * @return string|string[]|null
     */
    protected function compile_path($path,$template_data){
        preg_match_all("/( src=| href=| url\()\"(?!http)(?!#)(?!javascript)(?!{{)(.*?)\"/",$template_data,$url_matchs,PREG_SET_ORDER);
        $path=str_replace("\\","/",$this->file_path.dirname($path));
        $path=str_replace(config::project_path(true),config::project_path(false)."/",$path)."/";
        $index_path=config::index_path();
        foreach ($url_matchs as $value){
            if(trim($value[2])==''||trim($value[2])=="#"){
                continue;
            }
            if(strpos($value[2],'.') !== false){
                $template_data=common::str_replace_limit('"'.$value[2],'"'.$path.$value[2],$template_data,1);
            }
            else{
                $template_data=common::str_replace_limit('"'.$value[2],'"'.$index_path.$value[2],$template_data,1);
            }
        }
        return $template_data;
    }
    protected function compile_component($template_data){
        preg_match_all("/@component\((.*[\s\S])\)/",$template_data,$matchs,PREG_SET_ORDER);
        foreach ($matchs as $match){
            if(strpos($match[0],',')!==false){
                $index=strpos($match[1],",");
                $match_data=[];
                $match_data[0]=mb_substr($match[1],0,$index);
                $match_data[1]=mb_substr($match[1],$index+1,mb_strlen($match[1])-$index);
                $template_data=str_replace($match[0],"<?php echo self::\$object->get_tag_content('body',view('$match_data[0]',$match_data[1]),true);?>",$template_data);
                $this->get_component_content($match_data[0],true);
                $match[1]=$match_data[0];
            }
            else {
                $template_data = str_replace($match[0], $this->get_component_content($match[1], true), $template_data);
            }
            $this->template_link_info[config::env_path()."/public/template/".$match[1].".html"]=md5_file(config::env_path()."/public/template/".$match[1].".html");
        }
        return $template_data;
    }

    /**
     * @description 编译组件
     * @param $template_path
     * @param bool $is_full
     * @return mixed
     */
    public function get_component_content($template_path,$is_full=false){
        return $this->get_tag_content("body",$this->start_compile($template_path),$is_full);
    }

    /**
     * @description 获取某个标签里面的html5节点
     * @param $tag
     * @param $content
     * @param bool $is_full
     * @return mixed
     */
    protected function get_tag_content($tag,$content,$is_full=false){
        preg_match("/<$tag(.*?)>(.*[\s\S])<\/$tag>/is",$content,$template_component_mathchs);
        if($is_full) {
            return $template_component_mathchs[2];
        }
        else{
            return $template_component_mathchs[1];
        }
    }

    /**
     * @description 编译继承的模板
     * @param $template_data
     * @return string|string[]
     */
    protected function compile_extend($template_data){
        preg_match_all("/@extend\('(.*?)'\)(.*[\s\S])@endextend/is",$template_data,$matchs,PREG_SET_ORDER);
        foreach ($matchs as $match){
            $content=$this->get_component_content($match[1],true);
            $template_content=str_replace("@content",$match[2],$content);
            $template_data=str_replace($match[0],$template_content,$template_data);
        }
        return $template_data;
    }

    /**
     * @description 输出模板对应的meta
     * @param string $template_data
     * @return void
     */
    protected function compile_meta(string $template_data){
        preg_match_all("/<meta(.*?)>/is",$template_data,$matchs,PREG_SET_ORDER);
        foreach ($matchs as $item){
            $this->mata[]=$item[0];
        }
    }
}