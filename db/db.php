<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/16 0016
 * Time: 下午 2:59
 */
namespace db;
class db
{
    public $con;
    public function __construct()
    {
        $user="register";
        $password="zlj19971998";
        $this->con=mysqli_connect("localhost",$user,$password,"register");
        if(mysqli_connect_errno())
        {
            return "error";
        }
    }
    public function insert_databse($table_name,array $arr)
    {
        try {
            $number = count($arr);
            $start = 0;
            $column = "(";
            $values = "(";
            foreach ($arr as $key => $value) {
                $column = $column . "$key,";
                $values = $values . "'$value',";
            }
            $column = substr($column, 0, strlen($column) - 1);
            $values = substr($values, 0, strlen($values) - 1);
            $column = $column . ")";
            $values = $values . ")";
            $sql = "insert into $table_name $column values $values";
            echo $sql;
            if ($this->con->query($sql) == true) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $E) {
            return $E;
        }
    }
    public function query($table_name,array $arr,$condition){
        $sql="";
        $query_list="";
        $result_list=[];
        foreach ($arr as $value)
        {
            $query_list.="$value,";
        }
        $query_list=substr($query_list, 0, strlen($query_list) - 1);
        if($condition!=false){
            $sql="select $query_list from $table_name where $condition";
        }
        else{
            $sql="select $query_list from $table_name";
        }
        //echo $sql;
        $result=$this->con->query($sql);
        if($result==false)
        {
            return [];
        }
        if(mysqli_num_rows($result)==1) {
            foreach ($arr as $value)
            {
                $result_list[$value]=[];
            }
            while ($row = mysqli_fetch_array($result)) {
                foreach ($arr as $value){
                    $result_list[$value]=$row[$value];
                }
            }
        }
        else{
            while ($row = mysqli_fetch_array($result)) {
//                for ($i = 0; $i < count($arr); $i++) {
//                    array_push($result_list[$arr[$i]], $row[$arr[$i]]);
//                }
                $re=[];
                foreach ($arr as $value)
                {
                    $re[$value]=$row[$value];
                }
                $result_list[]=$re;
            }
        }
        return $result_list;
    }
    public function create_table($table_name,$arr){
        $sql="create table $table_name(";
            foreach ($arr as $key=>$value){
                $sql_list="$key $value";
                $sql.=$sql_list;
            }
            $sql=substr($sql,0,(strlen($sql)-1));
            $sql.=")";
            $this->con->query($sql);
    }
    public function drop_table($table_name){
        $sql="drop table $table_name";
        $this->con->query($sql);
    }
    public function update_table($table_name,$arr,$condition,$is_batch){
        if(!$is_batch)
        {
            $sql="update $table_name set";
            foreach ($arr as $key=>$value)
            {
                $sql.=" $key='$value',";
            }
            $sql=substr($sql,0,strlen($sql)-1);
            $sql=$sql." ".$condition;
        }
        return $this->con->query($sql);
    }
}
//$table_name="user";
//$arr=[
//    "password"=>"zzzzzzzzzz",
//    "sex"=>"woman"
//];
//$condition="where name='123456'";
//$db=new db();
//$db->update_table($table_name,$arr,$condition,false);