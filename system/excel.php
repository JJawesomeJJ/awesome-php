<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/13 0013
 * Time: 下午 9:46
 */

namespace system;


use load\auto_load;
use system\cache\cache;

class excel
{
    private $is_continue=true;
    public function __construct()
    {
        auto_load::load("excel.PHPExcel");
        auto_load::load("excel\PHPExcel\IOFactory");
//        ini_set('memory_limit','200M');
    }
    public function read_excel($file_name,$max_col=null,$max_row=null){
        $current_row_index=1;
        $current_col_index=0;
        $start_memory=memory_get_usage();
        if(!is_file($file_name)){
            return "is_not_a_file $file_name";
        }
        try {
            $cahce=new cache();
            if(($data=$cahce->get_cache(md5_file($file_name)))!=null){
                echo "load";
                return $data;
            }
            $data = [];
            $null=[];
            $inputFileType = \PHPExcel_IOFactory::identify($file_name);
            if ($inputFileType !== "Excel5" && $inputFileType !== "Excel2007") {
                return "EXCEL_TYPE_ERROR";
            }
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($file_name);
            \PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
            $sheet = $objPHPExcel->getSheet(0);
            // 取得总行数
            //$highestRow = $sheet->getHighestRow();
            // 取得总列数
            //$highestColumn = $sheet->getHighestColumn();
            //循环读取excel文件,读取一条,插入一条


            $allColumn = $sheet->getHighestColumn();        //**取得最大的列号*/
            $allRow = $sheet->getHighestRow();        //**取得一共有多少行*/
            $ColumnNum = \PHPExcel_Cell::columnIndexFromString($allColumn);     // 列号 转 列数
//            if(is_numeric($max_col)){
//                $ColumnNum=$max_col;
//            }//用户自定义行数
//            if(is_numeric($max_row)){
//                $allRow=$max_row;
//            }
            for ($rowIndex = $current_row_index; $rowIndex <= $allRow; $rowIndex++) {        //循环读取每个单元格的内容。注意行从1开始，列从A开始
                for ($colIndex = $current_col_index; $colIndex <= $ColumnNum; $colIndex++) {
                    $col = $sheet->getCellByColumnAndRow($colIndex, $rowIndex)->getValue();
                    if($col!=null) {
                        $data[] = [$rowIndex=>$col];
                    }
//                        if (memory_get_usage() > 77108864) {
//                            $current_row_index=$rowIndex;
//                            $current_col_index=$colIndex;
//                            if($rowIndex>=$allRow&&$colIndex>=$ColumnNum){
//                                $this->is_continue=false;
//                            }
//                            $objPHPExcel=null;
//                            $inputFileType = null;
//                            $objReader = null;
//                            $sheet = null;
//                            $allRow = null;
//                            $allColumn = null;
//                            break 2;
//                        }
                }

            }
            $cahce->set_cache(md5_file($file_name),$data,6400);
            return $data;
        } catch(\Throwable $e) {
            return $e->getMessage();
        }
    }
    public function write_excel($file_name,$data){

    }
}