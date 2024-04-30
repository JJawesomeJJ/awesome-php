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
        $user="root";
        $password="";
        $this->con=mysqli_connect("localhost",$user,$password,"register");
        mysqli_set_charset($this->con,"utf8");
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
            if ($this->con->query($sql) == true) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $E) {
            return $E;
        }
    }
    public function query($table_name,array $arr,$condition=false,$order_by="")
    {
        $sql = "";
        $query_list = "";
        $result_list = [];
        $link = "";
        $key_list=[];
        if (!$table_name) {
            $table_name_string = "";
            foreach ($arr["table_name"] as $key => $value) {
                $table_name_string .= "$key,";
                foreach ($value as $value1) {
                    $query_list .= "$key.$value1,";
                    $key_list[]=$value1;
                }
            }
            foreach ($arr["link"] as $key=>$value)
            {
                $link.="$key.$value=";
            }
            $table_name_string = substr($table_name_string, 0, strlen($table_name_string) - 1);
            $link = substr($link, 0, strlen($link) - 1);
            $query_list = substr($query_list, 0, strlen($query_list) - 1);
            if ($condition != false) {
                $sql = "select $query_list from $table_name_string where $link and $condition $order_by";
            } else {
                $sql = "select $query_list from $table_name_string where $link $order_by";
            }
            $arr=$key_list;
        }
        else {
            foreach ($arr as $value) {
                $query_list .= "$value,";
            }
            $query_list = substr($query_list, 0, strlen($query_list) - 1);
            if ($condition != false) {
                $sql = "select $query_list from $table_name where $condition $order_by";
            } else {
                $sql = "select $query_list from $table_name $order_by";
            }
        }
        $result=$this->con->query($sql.";");
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
    }//when $table_name==false the db->querey is multi-table queries use is as $db->query(false,["table_name"=>["comment_list"=>["user_id","comment_content"],"user"=>["head_img"]],"link"=>["comment_list"=>"user_id","user"=>"name"]]);
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
    public function delete_data($table_name,$condition){
        $sql="delete from $table_name where $condition";
        echo $sql;
        return $this->con->query($sql);
    }
    public function show_columns($table_name){
        $result_arr=[];
        $sql="SHOW FULL COLUMNS FROM $table_name";
        $result=$this->con->query($sql);
        while($row=mysqli_fetch_array($result)){
            $result_arr[]=$row[0];
        }
        return $result_arr;
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
