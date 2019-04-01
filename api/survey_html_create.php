<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/28 0028
 * Time: 下午 5:11
 */
require ("../admin/class/database.php");
require ("../admin/class/phpqrcode.php");
require ("../admin/cookie_login.php");
class survey_html_create
{
    public $con;
    function __construct()
    {
        $database2 = new database();
        $this->con = $database2->login_database("register");
    }

    public function get_post_info()
    {
        if (isset($_POST["name"]) && isset($_POST["id"]) && isset($_POST["answer"])&&isset($_POST["to"])) {
            $survey_table_name=$_POST["to"];
            $answer = explode(";", $_POST["answer"]);
            $name = $_POST["name"];
            $sql = "SELECT * FROM $survey_table_name";
            $this->con->query("LOCK TABLE $survey_table_name WRITE");
            $result = $this->con->query($sql);
            $i = 0;
            $arr_anwser = array(
                'A' => array(),
                'B' => array(),
                'C' => array(),
                'D' => array()
            );
            $arr_data_from_datase = array(
                'A' => array(),
                'B' => array(),
                'C' => array(),
                'D' => array()
            );
            /*$arr_data_from_datase['A']=array();
            $arr_data_from_datase['B']=array();
            $arr_data_from_datase['C']=array();
            $arr_data_from_datase['D']=array();
            $arr_anwser['A']=array();
            $arr_anwser['B']=array();
            $arr_anwser['C']=array();
            $arr_anwser['D']=array();
            */
            $end="WHERE q_num IN(";
            $i=0;
            while ($row = mysqli_fetch_array($result)) {
                $end.="'q".($i+1)."',";
                switch ($answer[$i]) {
                    case 'A':
                        array_push($arr_data_from_datase['A'], $row['A']);
                        array_push($arr_anwser['A'], "q" . ($i + 1));
                        break;
                    case 'B':
                        array_push($arr_data_from_datase['B'], $row['B']);
                        array_push($arr_anwser['B'], "q" . ($i + 1));
                        break;
                    case 'C':
                        array_push($arr_data_from_datase['C'], $row['C']);
                        array_push($arr_anwser['C'], "q" . ($i + 1));
                        break;
                    case 'D':
                        array_push($arr_data_from_datase['D'], $row['D']);
                        array_push($arr_anwser['D'], "q" . ($i + 1));
                        break;
                    default:
                        break;
                }
                $i++;
            }
            $sql_arr = array();
            $test=array();
            $update="update $survey_table_name SET ";
            foreach ($arr_anwser as $key => $val) {
                switch ($key) {
                    case 'A':
                        if (count($val) == 0) {
                            break;
                        }
                        $end="WHERE q_num IN(";
                        $sql_arr['A'] = $update."A = CASE q_num";
                        $arr_from = $arr_data_from_datase["A"];
                        for ($i = 0; $i < count($val); $i++) {
                            $num = (int)$arr_from[$i] + 1;
                            $sql_arr['A'] .= " WHEN '$val[$i]' THEN $num ";
                        }
                        $sql_arr['A'] .=" END ";
                        foreach ($arr_anwser['A'] as $key => $val) {
                            $end.="'$val',";
                        }
                        $end=substr($end, 0, -1).")";
                        $sql_arr['A'].=$end;
                        break;
                    case 'B':
                        if (count($val) == 0) {
                            break;
                        }
                        $end="WHERE q_num IN(";
                        $sql_arr['B'] = $update."B = CASE q_num";
                        $arr_from = $arr_data_from_datase["B"];
                        for ($i = 0; $i < count($val); $i++) {
                            $num = (int)$arr_from[$i] + 1;
                            $sql_arr['B'] .= " WHEN '$val[$i]' THEN $num ";
                        }
                        $sql_arr['B'] .=" END ";
                        foreach ($arr_anwser['B'] as $key => $val) {
                            $end.="'$val',";
                        }
                        $end=substr($end, 0, -1).")";
                        $sql_arr['B'].=$end;
                        break;
                    case 'C':
                        if (count($val) == 0) {
                            break;
                        }
                        $end="WHERE q_num IN(";
                        $sql_arr['C'] = $update."C = CASE q_num";
                        $arr_from = $arr_data_from_datase["C"];
                        for ($i = 0; $i < count($val); $i++) {
                            $num = (int)$arr_from[$i] + 1;
                            $sql_arr['C'] .= " WHEN '$val[$i]' THEN $num ";
                        }
                        $sql_arr['C'] .=" END ";
                        foreach ($arr_anwser['C'] as $key => $val) {
                            $end.="'$val',";
                        }
                        $end=substr($end, 0, -1).")";
                        $sql_arr['C'].=$end;
                        break;
                    case 'D':
                        if (count($val) == 0) {
                            break;
                        }
                        $end="WHERE q_num IN(";
                        $sql_arr['D'] = $update."D = CASE q_num";
                        $arr_from = $arr_data_from_datase["D"];
                        for ($i = 0; $i < count($val); $i++) {
                            $num = (int)$arr_from[$i] + 1;
                            $sql_arr['D'] .= " WHEN '$val[$i]' THEN $num ";
                        }
                        $sql_arr['D'] .=" END ";
                        foreach ($arr_anwser['D'] as $key => $val) {
                            $end.="'$val',";
                        }
                        $end=substr($end, 0, -1).")";
                        $sql_arr['D'].=$end;
                        break;
                    default;
                        break;
                }
            }
            foreach ($sql_arr as $key => $val) {
               $this->con->query($val);
            }

            //echo json_encode($sql_arr);
            $result = $this->con->query($sql);
            $index = 1;
            while ($row = mysqli_fetch_array($result)) {
                $result_arr = array();
                $result_arr["q" + $index] = $row['1'] . ";" . $row['2'] . ";" . $row['3'] . ";" . $row['4'];
                $index++;
                echo json_encode($result_arr);
            }
            $this->con->query("UNLOCK TABLES");
            //echo json_encode($result_arr);
        } else {
            $arr = array(
                "code" => "403",
                "data" => "forbidden"
            );
        }
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
        if(($this->con->query($sql))==true) {
            for ($i = 1; $i <= $q_num; $i++) {
                $q = "q" . "$i";
                $sql = "insert into $table_name (q_num,A,B,C,D)value('$q','0','0','0','0')";
                $this->con->query($sql);
            }
            return true;
        }
        else{
            return false;
        }
    }

    public function template_page_create($arr,$writer,$title)
    {
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
        $myfile = fopen("survey_html.html", "r") or die("Unable to open file!");
        $str=fread($myfile,filesize("survey_html.html"));
        fclose($myfile);
        $html=str_replace("&&question&&;",$html,$str);
        $html=str_replace("&&作者&&",$writer,$html);
        $html=str_replace("&&title&&",$title,$html);
        $html=str_replace("&&&TPI&",$tpi,$html);
        $create = fopen("/var/www/html/page/survey/".$title."_".$writer.".html", "w");
        fwrite($create,$html);
        fclose($create);
    }
    public function delete_database_file($name)
    {
        session_start();
        $sql = "DROP TABLE $name";
        $this->con->query($sql);
        $writer = $_SESSION['user'];
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
    public function test(){
        $writer1="122"."_"."kkk";
        $name="kkk";
        $time=date("Y-m-d");
        $arr = array(
            "code" => "200",
            "data" => "ok"
        );
        $arr_data=json_encode($arr);
        $sql="insert into survey(writer,survey_name,data,time,flag)values('$writer1','$name','$arr_data','$time','on_survey')";
        $this->con->query($sql);
    }
    public function get_survey_info(){
        $number=0;
        $number_list=[];
        $data=array();
        $writer=$_SESSION['user'];
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
            while ($row = mysqli_fetch_array($result)) {
                $index = $number_list[$i];
                $data[$index]["num"] = $row['A'] + $row['B'] + $row['C'] + $row['D'];
                $i = $i + 1;
            }
        }
        echo json_encode($data);
    }
    public function draw_survey($name){
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
            $sql="drop table $name";
            $this->con->query($sql);
            $arr = array(
                "code" => "200",
                "message" => "ok"
            );
            echo json_encode($arr);
        }
        catch (Exception $E)
        {
            $arr = array(
                "code" => "403",
                "message" => "forbbiden"
            );
            echo json_encode($arr);
            return;
        }
    }
    public function log_excel(){

    }
}
$survey=new survey_html_create();
if($_POST['type']!='update_sql'&&$_POST['type']!='query_survey_info')
{
    $login=new cookie_login($survey->con);
}
switch ($_POST['type']){
    case 'update_sql':
        $survey->get_post_info();
        break;
    case "query_survey_info":
        $to=$_POST['to'];
        $sql="select result from survey where survey_name='$to'";
        $result=$survey->con->query($sql);
        while($row=mysqli_fetch_array($result))
        {
            echo $row['result'];
        }
        break;
    case "create":
        session_start();
        if ((strtolower($_SESSION["email_code"]))==(strtolower($_POST["code"]))) {
            try {
                $arr = $_POST["question_arr"];
                try {
                    $survey->template_page_create($arr, $_SESSION["user"], $_POST["title"]);
                    $arr = array(
                        "code" => "200",
                        "data" => "ok"
                    );
                }
                catch (Exception $E)
                {
                    $arr = array(
                        "code" => "403",
                        "data" => "op_error"
                    );
                }
                echo json_encode($arr);
            } catch (Exception $E) {
                $arr = array(
                    "code" => "403",
                    "data" => "forbidden"
                );
                echo json_encode($arr);
            }
        }
        else{
            //$survey->test();
        }
        break;
    case 'delete':
        $name=$_POST['name'];
        $survey->delete_database_file($name);
        break;
    case "get_my_survey":
        $survey->get_survey_info();
        break;
    case "draw":
        $survey->draw_survey($_POST["name"]);
        break;
    default:
        $arr = array(
            "code" => "403",
            "data" => "forbidden"
        );
        echo json_encode($arr);
}
$arr = array(
        "（1）马克思主义的理论体系构成" => '马克思主义哲学;马克思主义政治经济学;社会主义科学;以上都是',
        "（2）关于马克思主义一下说法那个不对" => '广义上，马克思主义是由马克思恩格斯创立，并未后继者不断发展的科学理论体系;狭义上是马克思和恩格斯创立的基本理论，基本观点和学说上体系;原理：是对马克思主义立场，观点方法的集中概括;以上都不对',
        "（3）马克思主义的直接来源" => '一下都是;德国古典哲学;英国古典政治经济学;，英法两国的空想主义',
        "（4）唯物主义发展形态一下那个不正确" => '古代朴素唯物主义;近代形而上学唯物主义;辩证唯物主义;现代唯物主义',
        " (5)一下关于物质的概念不正确的是" => '所谓物质就是不依赖人的意思而存在并能为人的意思所反映;平时上：指物物质的具体形态，是自然界存在的事务;从哲学上：不仅包括感知的自然事务，好包括从感觉上感知的人的实践活动;以上都是',
        "（6）马克思主义的物质范畴理论具有的意义" => '坚持了唯物主义一元论，同唯心主义一元论和二元论划清了界限;坚持了能动的反映论和可知论;都是对的;体现了唯物论和辩证法的统一，克服了形而上学的唯物主义的缺陷',
        "（7）运动和静止的区别的错误选项是" => '静止是运动的特殊状态，动中有静，静中有动;运动是宇宙中一切事物的变化及其过程;静止是事物空间的相对位置暂时不变和事物的根本性质暂时不变;②运动是绝对的、无条件的，而静止是相对的，无条件的',
        "（8）唯物主义辩证法的基本范畴错误说法是" => '内容与形式是从构成要素和表现方式上反映事物的一对基本范畴;世界上任何事物的存在和发展都是绝对运动和相对静止的统一;原因与结果揭示事物引起和被引起关系的一对范畴;原因与结果揭示事物引起和被引起关系的一对范畴。',
        "（9）矛盾的普遍性和特殊性及其辩证关系" => '矛盾的普遍性是指矛盾存在于一切事物中，存在于一切事物发展过程的始终;以上都对;矛盾的特殊性是指各个具体事物的矛盾、每一个矛盾的各个方面在发展的不同 ;每一个矛盾的各个方面在发展的不同 阶段上各有其特点',
        "（10）实践的三个基本特征错误的是" => '直接现实性;社会政治实践;自觉能动性;科学文化实践',
        "（11）实践的三个基本特征" => '以下都对;自觉能动性;社会历史性;直接现实性',
    );
//$survey->template_page_create($arr, "赵李杰","关于马克思主义的问卷调查");

//if($_POST['type']=='update_sql')
//{
//    $survey->get_post_info();
//    return;
//}
//if($_POST["type"]=="query_survey_info")
//{
//    $to=$_POST['to'];
//    $sql="select result from survey where survey_name='$to'";
//    $result=$survey->con->query($sql);
//    while($row=mysqli_fetch_array($result))
//    {
//        echo $row['result'];
//    }
//    return;
//}
//if($_POST["type"]=="create") {
////    $survey->delete_database_file("关于马克思主义的问卷调查-by");
////    $arr = array(
////        "（1）马克思主义的理论体系构成" => '马克思主义哲学;马克思主义政治经济学;社会主义科学;以上都是',
////        "（2）关于马克思主义一下说法那个不对" => '广义上，马克思主义是由马克思恩格斯创立，并未后继者不断发展的科学理论体系;狭义上是马克思和恩格斯创立的基本理论，基本观点和学说上体系;原理：是对马克思主义立场，观点方法的集中概括;以上都不对',
////        "（3）马克思主义的直接来源" => '一下都是;德国古典哲学;英国古典政治经济学;，英法两国的空想主义',
////        "（4）唯物主义发展形态一下那个不正确" => '古代朴素唯物主义;近代形而上学唯物主义;辩证唯物主义;现代唯物主义',
////        " (5)一下关于物质的概念不正确的是" => '所谓物质就是不依赖人的意思而存在并能为人的意思所反映;平时上：指物物质的具体形态，是自然界存在的事务;从哲学上：不仅包括感知的自然事务，好包括从感觉上感知的人的实践活动;以上都是',
////        "（6）马克思主义的物质范畴理论具有的意义" => '坚持了唯物主义一元论，同唯心主义一元论和二元论划清了界限;坚持了能动的反映论和可知论;都是对的;体现了唯物论和辩证法的统一，克服了形而上学的唯物主义的缺陷',
////        "（7）运动和静止的区别的错误选项是" => '静止是运动的特殊状态，动中有静，静中有动;运动是宇宙中一切事物的变化及其过程;静止是事物空间的相对位置暂时不变和事物的根本性质暂时不变;②运动是绝对的、无条件的，而静止是相对的，无条件的',
////        "（8）唯物主义辩证法的基本范畴错误说法是" => '内容与形式是从构成要素和表现方式上反映事物的一对基本范畴;世界上任何事物的存在和发展都是绝对运动和相对静止的统一;原因与结果揭示事物引起和被引起关系的一对范畴;原因与结果揭示事物引起和被引起关系的一对范畴。',
////        "（9）矛盾的普遍性和特殊性及其辩证关系" => '矛盾的普遍性是指矛盾存在于一切事物中，存在于一切事物发展过程的始终;以上都对;矛盾的特殊性是指各个具体事物的矛盾、每一个矛盾的各个方面在发展的不同 ;每一个矛盾的各个方面在发展的不同 阶段上各有其特点',
////        "（10）实践的三个基本特征错误的是" => '直接现实性;社会政治实践;自觉能动性;科学文化实践',
////        "（11）实践的三个基本特征" => '以下都对;自觉能动性;社会历史性;直接现实性',
////    );
//    session_start();
//    if ((strtolower($_SESSION["email_code"]))==(strtolower($_POST["code"]))) {
//        try {
//            $arr = $_POST["question_arr"];
//            try {
//                $survey->template_page_create($arr, $_SESSION["user"], $_POST["title"]);
//                $arr = array(
//                    "code" => "200",
//                    "data" => "ok"
//                );
//            }
//            catch (Exception $E)
//            {
//                $arr = array(
//                    "code" => "403",
//                    "data" => "op_error"
//                );
//            }
//            echo json_encode($arr);
//        } catch (Exception $E) {
//            $arr = array(
//                "code" => "403",
//                "data" => "forbidden"
//            );
//            echo json_encode($arr);
//        }
//    }
//    else{
//        //$survey->test();
//    }
//}
//if($_POST['type']=='delete')
//{
//    $name=$_POST['name'];
//    $survey->delete_database_file($name);
//}
//if($_POST['type']=='get_my_survey')
//{
//    $survey->get_survey_info();
//}
//else{
//    //$survey->delete_database_file("关于马克思主义的问卷调查-by");
////    $arr = array(
////        "（1）马克思主义的理论体系构成" => '马克思主义哲学;马克思主义政治经济学;社会主义科学;以上都是',
////        "（2）关于马克思主义一下说法那个不对" => '广义上，马克思主义是由马克思恩格斯创立，并未后继者不断发展的科学理论体系;狭义上是马克思和恩格斯创立的基本理论，基本观点和学说上体系;原理：是对马克思主义立场，观点方法的集中概括;以上都不对',
////        "（3）马克思主义的直接来源" => '一下都是;德国古典哲学;英国古典政治经济学;，英法两国的空想主义',
////        "（4）唯物主义发展形态一下那个不正确" => '古代朴素唯物主义;近代形而上学唯物主义;辩证唯物主义;现代唯物主义',
////        " (5)一下关于物质的概念不正确的是" => '所谓物质就是不依赖人的意思而存在并能为人的意思所反映;平时上：指物物质的具体形态，是自然界存在的事务;从哲学上：不仅包括感知的自然事务，好包括从感觉上感知的人的实践活动;以上都是',
////        "（6）马克思主义的物质范畴理论具有的意义" => '坚持了唯物主义一元论，同唯心主义一元论和二元论划清了界限;坚持了能动的反映论和可知论;都是对的;体现了唯物论和辩证法的统一，克服了形而上学的唯物主义的缺陷',
////        "（7）运动和静止的区别的错误选项是" => '静止是运动的特殊状态，动中有静，静中有动;运动是宇宙中一切事物的变化及其过程;静止是事物空间的相对位置暂时不变和事物的根本性质暂时不变;②运动是绝对的、无条件的，而静止是相对的，无条件的',
////        "（8）唯物主义辩证法的基本范畴错误说法是" => '内容与形式是从构成要素和表现方式上反映事物的一对基本范畴;世界上任何事物的存在和发展都是绝对运动和相对静止的统一;原因与结果揭示事物引起和被引起关系的一对范畴;原因与结果揭示事物引起和被引起关系的一对范畴。',
////        "（9）矛盾的普遍性和特殊性及其辩证关系" => '矛盾的普遍性是指矛盾存在于一切事物中，存在于一切事物发展过程的始终;以上都对;矛盾的特殊性是指各个具体事物的矛盾、每一个矛盾的各个方面在发展的不同 ;每一个矛盾的各个方面在发展的不同 阶段上各有其特点',
////        "（10）实践的三个基本特征错误的是" => '直接现实性;社会政治实践;自觉能动性;科学文化实践',
////        "（11）实践的三个基本特征" => '以下都对;自觉能动性;社会历史性;直接现实性',
////    );
//    //$survey->template_page_create($arr,"zlh", "xixix");
////    echo "lll";
//    //echo json_encode($arr);
//    //$survey->test();
//    //$survey->get_survey_info();
//}
////echo json_encode($arr);