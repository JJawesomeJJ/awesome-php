<?php
require ("../admin/excel/PHPExcel.php");
require ("../admin/class/database.php");
class log_excel
{
    private $con;
    public function __construct()
    {
        $database2 = new database();
        $this->con = $database2->login_database("register");
    }
    public function query_data($name,$writer){
        $arr=[];
        $sql="select * from survey where survey_name='$name' and writer='$writer'";
        $result=$this->con->query($sql);
        while($row=mysqli_fetch_array($result)) {
                $arr[] = array(
                    "name" => $name,
                    "time" => $row['time'],
                    "state" => $row["flag"],
                    "writer" => $row["writer"],
                    "data" => $row["data"]
                );
            if($row['result']==""){
                    $sql = "select * from $name";
                    $result = $this->con->query($sql);
                    while ($row = mysqli_fetch_array($result)) {
                        $data_list = [];
                        $data_list['A'] = $row['A'];
                        $data_list['B'] = $row['B'];
                        $data_list['C'] = $row['C'];
                        $data_list['D'] = $row['D'];
                        $data_list['E']="无";
                        $arr[] = $data_list;
                    }
                }
                else{
                    $str=$row['result'];
                    $str=str_replace("{","",$str);
                    $str=str_replace("}","",$str);
                    $re_list=explode("\"\"",$str);
                    $i=0;
                    foreach ($re_list as $key=>$value) {
                        $data_list = [];
                        $re_list[$i] = preg_replace("/(\d+):/", "", str_replace("\"", "", $re_list[$i]));
                        $result_arr = explode(";", $re_list[$i]);
                        $KEY = 'A';
                        foreach ($result_arr as $key => $value) {
                            $data_list[$KEY++] = $value;
                        }
                        $data_list[$KEY]='无';
                        $arr[] = $data_list;
                        $i++;
                    }
                }
            }
        return $arr;
    }
    public function log_data($name,$writer){
        $arr=$this->query_data($name,$writer);
        $num=0;
        foreach ($arr[1] as $key=>$value)
        {
            $num=$num+$value;
        }
        $cfg_columns = array(
            'A'=>'题号',
            'B'=>'A/次数',
            'C'=>'B/次数',
            'D'=>'C/次数',
            'E'=>'D/次数',
            'F'=>'备注'
        );
        ob_end_clean();
        $filename = $name.'.xls';
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="'.$filename.'"');
        header('Content-Disposition:inline;filename="'.$filename.'"');

        $objPHPExcel = new PHPExcel();
        $len=count($arr)+3;
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:G1');
        $objPHPExcel->getActiveSheet()->getStyle("A1:G$len")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $export_time = date('Y-m-d H:i:s');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1',$name);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold();
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);// 设置文字颜色
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', '参与人数');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B2', $num);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C2', '作者');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', $arr[0]["writer"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E2', '当前状态');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F2', $arr[0]["state"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A3', '创建时间');
        $objPHPExcel->getActiveSheet(0)->mergeCells('B3:C3');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', $arr[0]["time"]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', '导出时间');
        $objPHPExcel->getActiveSheet(0)->mergeCells('E3:F3');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E3', $export_time);
        $cols = 'A';
        foreach ($cfg_columns as $val){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols++.'4',$val);
        }
        $cols = 'A';
        $row=5;
        $number=1;
        foreach ($arr as $k => $val) {
            if($number==1){
                $number++;
                continue;
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cols++.$row,"问题".($number-1));
            $objPHPExcel->getActiveSheet()->setCellValue($cols++.$row,$val['A']);
            $objPHPExcel->getActiveSheet()->setCellValue($cols++.$row,$val['B']);
            $objPHPExcel->getActiveSheet()->setCellValue($cols++.$row,$val['C']);
            $objPHPExcel->getActiveSheet()->setCellValue($cols++.$row,$val['D']);
            $objPHPExcel->getActiveSheet()->setCellValue($cols++.$row,$val['E']);
            $number++;$row++;$cols='A';
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
}
}
session_start();
$name=explode("_",$_GET["survey_name"]);
if(isset($_SESSION['user'])&&($_SESSION['user']==$name[count($name)-1])) {
    $log = new log_excel();
    $log->log_data($_GET["survey_name"],$_SESSION['user']);
}
else{
    $arr = array(
        "code" => "403",
        "message" => "forbbiden"
    );
    echo json_encode($arr);
}