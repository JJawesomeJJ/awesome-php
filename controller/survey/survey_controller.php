<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/18 0018
 * Time: 下午 8:10
 */

namespace controller\survey;


use controller\auth\auth_controller;
use controller\controller;
use request\request;
use task\add_task;

class survey_controller extends controller
{
    private $con;
    private $redis;
    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect("127.0.0.1", 6379);
        $this->con=mysqli_connect("localhost","register","zlj19971998","register");
    }
    public function vote()
    {
        $redis = new \Redis();
        $redis->connect("127.0.0.1", 6379);
        $request = new request([]);
        $arr = ["controller_name" => "survey_html_create", "method" => "get_post_info", "arg" => ["to" => $request->get("to"), "answer" => $request->get("answer")]];
        $task = new add_task();
        $task->add("survey_html_create", "get_post_info", ["to" => $request->get("to"), "answer" => $request->get("answer")]);
        $survey_table_name = $request->get("to");
        $sql = "SELECT * FROM $survey_table_name";
        $result = $this->con->query($sql);
        $index = 1;
        while ($row = mysqli_fetch_array($result)) {
            $result_arr = array();
            $result_arr["q" + $index] = $row['1'] . ";" . $row['2'] . ";" . $row['3'] . ";" . $row['4'];
            $index++;
            echo json_encode($result_arr);
        }
    }
    public function get_survey_info(){
        $number=0;
        $number_list=[];
        $writer=auth_controller::auth("user");
        $sql="select * from survey where writer='$writer'";
        $result=$this->con->query($sql);
        $sql_list="select * from %s where q_num='q1'";
        $sql_all="";
        while($row=mysqli_fetch_array($result))
        {
            if($row['result']=="") {
                $row_list = array(
                    "name" => $row['survey_name'],
                    "state" => $row["flag"],
                    "time" => $row["time"],
                    "num" => ""
                );
                $number_list[]=$number;
                if($sql_all==""){
                    $sql_all=sprintf($sql_list,$row['survey_name']);
                }
                else{
                    $sql_all.=" "."union all"." ".sprintf($sql_list,$row['survey_name']);
                }
            }
            else{
                $str=$row['result'];
                $str=str_replace("{","",$str);
                $str=str_replace("}","",$str);
                $re_list=explode("\"\"",$str);
                $re_list[0] = preg_replace("/(\d+):/", "", str_replace("\"", "", $re_list[0]));
                $result_arr = explode(";", $re_list[0]);
                $all=0;
                foreach ($result_arr as $key=>$value){
                    $all=$all+$value;
                }
                $row_list = array(
                    "name" => $row['survey_name'],
                    "state" => $row["flag"],
                    "time" => $row["time"],
                    "num" => $all
                );
            }
            $data[]=$row_list;
            $number++;
        }
        if(strlen($sql_all)>0) {
            $i = 0;
            $result = $this->con->query($sql_all);
            if($result!=null) {
                while ($row = mysqli_fetch_array($result)) {
                    $index = $number_list[$i];
                    $data[$index]["num"] = $row['A'] + $row['B'] + $row['C'] + $row['D'];
                    $i = $i + 1;
                }
            }
        }
        return $data;
    }
    public function draw_survey(){
        auth_controller::auth('user');
        $request=new request(["name"=>"required:post"]);
        $name=$request->get("name");
        try {
            $data = "";
            $myfile = fopen("/var/www/html/page/survey/" . $name . ".html", "r") or die("Unable to open file!");
            $str = fread($myfile, filesize("/var/www/html/page/survey/" . $name . ".html"));
            $str = preg_replace("/<div class=\"q1\">([\s\S]*?)\"学号\">/", "<div class=\"user_info\">
        <input type=\"text\" class=\"name\" placeholder=\"姓名\" style='display: none;'><br/>
        <input type=\"number\" class=\"id\" placeholder=\"学号\"style='display: none;'>", $str);
            $str = str_replace("提交", "投票通道关闭,点击查看结果", $str);
            fclose($myfile);
            file_put_contents("/var/www/html/page/survey/" . $name . ".html", $str);
            $sql = "select * from $name";
            $result = $this->con->query($sql);
            $index = 1;
            while ($row = mysqli_fetch_array($result)) {
                $result_arr = array();
                $result_arr["q" + $index] = $row['1'] . ";" . $row['2'] . ";" . $row['3'] . ";" . $row['4'];
                $index++;
                $data .= json_encode($result_arr);
            }
            $sql = "update survey set result='$data',flag='withdraw' where survey_name='$name'";
            $this->con->query($sql);
            $sql = "drop table $name";
            $this->con->query($sql);
            $arr = array(
                "code" => "200",
                "message" => "ok"
            );
            return $arr;
        }
        catch (Exception $E)
        {
            $arr = array(
                "code" => "403",
                "message" => "forbbiden"
            );
            return $arr;
        }
    }
    public function delete_database_file()
    {
        $writer=auth_controller::auth('user');
        $request=new request([]);
        $name=$request->get("name");
        session_start();
        $sql = "DROP TABLE $name";
        $this->con->query($sql);
        $sql = "delete from survey where survey_name='$name' and writer='$writer'";
        if ($this->con->query($sql) == true) {
            unlink("/var/www/html/page/survey/" . $name . ".html");
            $arr = array(
                "code" => "200",
                "message" => "ok"
            );
            echo json_encode($arr);
            return;
        }
        $arr = array(
            "code" => "403",
            "message" => "forbidden"
        );
        echo json_encode($arr);
    }
    public function template_page_create()
    {
        $rules=[
            "question_arr"=>"required:post",
            "title"=>"required:post",
            "code"=>"required:post",
        ];
        $request=new request($rules);
        $arr=$request->get("question_arr");
        $writer=auth_controller::auth('user');
        $title=$request->get("title");
        $request->unique("survey_name",$title."_".$writer,'survey');
        if(($this->create_table($title."_".$writer,count($arr)))==true){
        }
        $writer1=$title."_".$writer;
        $name=$writer;
        $time=date("Y-m-d H:i:s");
        $arr_data=json_encode($arr);
        $sql="insert into survey(writer,survey_name,data,time,flag)values('$name','$writer1','$arr_data','$time','on_survey')";
        $this->con->query($sql);
        $index = 1;
        $html = "";
        foreach ($arr as $key => $val) {
            $answer_list = explode(";", $val);
            $template="<div class=\"q1\">
        <h1>%s</h1>
        <input type=\"radio\" name=\"q6\" class=\"anwser\" value=\"A\">
        <label>A%s</label></br>
        <input type=\"radio\" name=\"q6\" class=\"anwser\" value=\"B\">
        <label>B%s</label></br>
        <input type=\"radio\" name=\"q6\" class=\"anwser\" value=\"C\">
        <label>C%s</label></br>
        <input type=\"radio\" name=\"q6\" class=\"anwser\" value=\"D\">
        <label>D%s</label></br>
    </div>";
            $html.=str_replace("q6","q".$index,sprintf($template,$key,$answer_list[0],$answer_list[1],$answer_list[2],$answer_list[3]));
            $index++;
        }
        $arr["title"]=$title."_".$writer;
        $tpi='<script type="text/html" id="TPI">'.json_encode($arr).'</script>';
        $myfile = fopen("/var/www/html/php/api/survey_html1.html", "r") or die("Unable to open file!");
        $str=fread($myfile,filesize("/var/www/html/php/api/survey_html1.html"));
        fclose($myfile);
        $html=str_replace("&&question&&;",$html,$str);
        $html=str_replace("&&作者&&",$writer,$html);
        $html=str_replace("&&title&&",$title,$html);
        $html=str_replace("&&&TPI&",$tpi,$html);
        $create = fopen("/var/www/html/page/survey/".$title."_".$writer.".html", "w");
        fwrite($create,$html);
        fclose($create);
        return ["code"=>200,"data"=>"ok"];
    }
    public function create_table($table_name, $q_num)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (" .
            "`q_num` TEXT NOT NULL," .
            "`A` INTEGER UNSIGNED NOT NULL," .
            "`B` INTEGER UNSIGNED NOT NULL," .
            "`C` INTEGER UNSIGNED NOT NULL," .
            "`D` INTEGER UNSIGNED NOT NULL" .
            ")ENGINE=InnoDB DEFAULT CHARSET=utf8";
        if (($this->con->query($sql)) == true) {
            for ($i = 1; $i <= $q_num; $i++) {
                $q = "q" . "$i";
                $sql = "insert into $table_name (q_num,A,B,C,D)value('$q','0','0','0','0')";
                $this->con->query($sql);
            }
            return true;
        } else {
            return false;
        }
    }
}