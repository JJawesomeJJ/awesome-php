<?php
/**
 * Created by jjawesome.
 * User: Administrator
 * Date: 2019/4/30 0030
 * Time: 下午 3:08
 */
namespace db\factory;
use extend\PHPMailer\PHPMailer;
use request\request;
use system\cache\cache;
use system\common;
use system\config\config;
use system\Exception;

class soft_db
{
    protected static $object_list=[];
    //对象列表避免多次实例化浪费资源
    protected $table_name=null;
    //表名
    protected static $table_name_=null;
    protected $where_="";
    //设置查询条件
    protected $order_by_="";
    //设置排序规则
    protected $limit_="";
    //设置分页
    public $query_list=[];
    //设置查询列表
    protected $join_="";
    //联合查询
    protected $group_by_="";
    //设置聚合查询
    private $con;
    //数据库连接对象
    protected $cache=null;
    //缓存对象
    protected $create_table_column_list=[];
    //创建表的列表字段
    protected $set_list=[];
    //更新字段
    protected $databse_name=null;
    //数据库名
    protected $having='';
    //聚合条件函数字段
    protected $bind=[];
    public function __construct()
    {
        $this->init();
    }
    protected function init(){
        $database=config::pdo();
        $driver=$database["driver"];
        $user=$database[$driver]["username"];
        $password=$database[$driver]["password"];
        $this->databse_name=$database[$driver]["database"];
        $host=$database[$driver]['hostname'];
        $dsn="$driver:host=$host;dbname=$this->databse_name;charset=utf8";
        $this->con = new \PDO($dsn, $user,$password);
        $this->con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
//        $this->con=mysqli_connect(config::database()["hostname"],$user,$password,$this->databse_name);
//        mysqli_set_charset($this->con,"utf8");
        //加载config数据库的配置
        $this->table_name=self::$table_name_;
    }
    public function refresh(){
        $this->where_="";
        $this->order_by_="";
        $this->limit_="";
        $this->set_list="";
        $this->create_table_column_list=[];
        $this->set_list=[];
        $this->join_="";
        $this->group_by_="";
    }
    //操作后刷新条件
    private function cache(){
        if ($this->cache==null){
            $this->cache=make('cache');
            return $this->cache;
        }
        return $this->cache;
    }
    public function all($is_refresh=false){
        $table_column=$this->cache()->get_cache($this->table_name."column");
        if($table_column==null||$is_refresh){
            $table_column=$this->get_table_column();
            $this->cache()->set_cache($this->table_name."column",$table_column,"forever");
        }
        $this->query_list=$table_column;
        return $this;
    }//查看缓存中是否存在该数据库的字段如果不存在则查询并载入缓存。当数据库的字段有更新时使用all(true) 来刷新字段
    //获取数据库的字段
    public static function table($table_name){
        self::$table_name_=$table_name;
        if(array_key_exists($table_name,self::$object_list)){
            $object=self::$object_list[$table_name];
            $object->refresh();
            return $object;
        }
        else{
            $object=new soft_db();
            self::$object_list[$table_name]=$object;
            return $object;
        }
    }//入口程序
    public function where($column_value,$condition_value,$condition="="){//defalut condition "=" you can use ">=,<=,like,in"
        $condition_key=$this->unique_key($condition_value);
        if($this->where_==""){
            $this->where_="where $column_value $condition $condition_key";
        }
        else{
            $this->where_=$this->where_." and $column_value $condition $condition_key";
        }
        return $this;
    }//设置条件
    public function where_like($column_value,$condition_value){
        $this->where($column_value,"%$condition_value%","like");
        return $this;
    }
    //模糊查询
    //defalut condition "=" you can use ">=,<=,like,in"
    public function or_where($column_value,$condition_value,$condition="="){
        $key=$this->unique_key($condition_value);
        if($this->where_==""){
            $this->where_="where $column_value $condition $key";
        }
        else{
            $this->where_=$this->where_." or $column_value $condition $key";
        }
        return $this;
    }
    public function join($join_table_name,$join_table_column,$table_name_link){
        $this->join_.="inner join $join_table_name on $join_table_name.$join_table_column=$this->table_name.$table_name_link";
        return $this;
    }//连接查询
    public function order_by($order_by_column,$sort_asc=true){
        if($sort_asc==true){
            $sort_asc="asc";
        }
        else{
            $sort_asc="desc";
        }
        $this->order_by_="order by $order_by_column $sort_asc";
        return $this;
    }//set order by rule desc or asc
    public function sava(){

    }
    public function delete(){
        $sql="delete from $this->table_name $this->where_";
        $sql=str_replace("  "," ",$sql);
        $sql=str_replace(", "," ",$sql);
        $stm=$this->con->prepare($sql);
        $this->refresh();
        return $stm->execute($this->bind_params($sql));
    }//删除数据
    private function is_1_array(array $arr){
        if (count($arr) == count($arr, 1)) {
            return true;
        } else {
            return false;
        }
    }
    public function insert(array $insert_data){
        $insert_list=null;
        $insert_string="";
        $insert_list_string="(";
        if($this->is_1_array($insert_data)){
            $insert_list=array_keys($insert_data);
            $insert_string_column="(";
            foreach ($insert_data as $value){
                $value_key=$this->unique_key($value);
                $insert_string_column.="$value_key,";
            }
            $insert_string_column.=")";
            $insert_string.=$insert_string_column;
        }
        else{
            $insert_list=array_keys($insert_data[0]);
            foreach ($insert_data as $insert_value){
                $insert_string_column="(";
                foreach ($insert_value as $value){
                    $value_key=$this->unique_key($value);
                    $insert_string_column.="$value_key,";
                }
                $insert_string_column.="),";
                $insert_string.=$insert_string_column;
            }
            $insert_string=substr($insert_string,0,strlen($insert_string)-1);
        }
        foreach ($insert_list as $value){
            $insert_list_string.="$value,";
        }
        $insert_list_string.=")";
        $sql="INSERT INTO  $this->table_name $insert_list_string values $insert_string";
        $sql=str_replace("  "," ",$sql);
        $sql=str_replace(", "," ",$sql);
        $sql=str_replace(",)",")",$sql);
        $stm=$this->con->prepare($sql);
        $result=$stm->execute($this->bind_params($sql));
        return $result;
    }
    public function limit($start_row,$num){
        if(!is_numeric($start_row)){
            new Exception("403","start_row_reqired_number_but".$start_row."_given");
        }
        if(!is_numeric($num)){
            new Exception("403","num_reqired_number_but_$start_row"."_given");
        }
        $this->limit_="limit $start_row,$num";
        return $this;
    }
    public function or_where_in($column_name,array $arr_condition){
        $arr_condition_string="(";
        if(!empty($arr_condition)) {
            foreach ($arr_condition as $value) {
                $arr_condition_string .= $this->unique_key($value) . ',';
            }
            $arr_condition_string = substr($arr_condition_string, 0, strlen($arr_condition_string) - 1);
            $arr_condition_string .= ")";
        }
        else{
            $arr_condition_string="('')";
        }
        if($this->where_==""){
            $this->where_="where $column_name in ".$arr_condition_string;
        }
        else{
            $this->where_.=" or $column_name in ".$arr_condition_string;
        }
        return $this;
    }
    public function where_in($column_name,array $arr_condition){
        $arr_condition_string="(";
        if(!empty($arr_condition)) {
            foreach ($arr_condition as $value) {
                $arr_condition_string .= $this->unique_key($value) . ',';
            }
            $arr_condition_string = substr($arr_condition_string, 0, strlen($arr_condition_string) - 1);
            $arr_condition_string .= ")";
        }
        else{
            $arr_condition_string="('')";
        }
        if($this->where_==""){
            $this->where_="where $column_name in ".$arr_condition_string;
        }
        else{
            $this->where_.=" and $column_name in ".$arr_condition_string;
        }
        return $this;
    }
    public function distinct(){
        return $this;
    }
    public function select(...$query_list){
        if(!$this->is_1_array($query_list)){
            $query_list=$query_list[0];
        }
        $this->query_list=$query_list;
        return $this;
    }
    public function get_select_column(){
        return $this->query_list;
    }
    public function where_between($column_name,$min,$max,$is_not=false){
        if($is_not==false){
            $is_not="";
        }
        else{
            $is_not="NOT";
        }
        if($this->where_==""){
            $this->where_="where $column_name $is_not between ".$this->unique_key($min)." and ".$this->unique_key($max);
        }
        else{
            $this->where_="and $column_name $is_not between ".$this->unique_key($min)." and ".$this->unique_key($max);
        }
        return $this;
    }
    public function or_where_between($column_name,$min,$max,$is_not=false){
        if($is_not==false){
            $is_not="";
        }
        else{
            $is_not="NOT";
        }
        if($this->where_==""){
            $this->where_="where $column_name $is_not between ".$this->unique_key($min)." and ".$this->unique_key($max);
        }
        else{
            $this->where_.=" or $column_name $is_not between ".$this->unique_key($min)." and ".$this->unique_key($max);
        }
        return $this;
    }
    public function count($column_name){
        $this->query_list[]="count($column_name) as count";
        return $this;
    }
    public function min($column_name){
        $this->query_list[]="min($column_name) as min";
        return $this;
    }
    public function max($column_name){
        $this->query_list[]="max($column_name) as max";
        return $this;
    }
    public function sum($column_name){
        $this->query_list[]="sum($column_name) as sum";
        return $this;
    }
    public function avg($column_name){
        $this->query_list[]="avg($column_name) as avg";
        return $this;
    }
    public function group_by($column_name){
        $this->group_by_="group by ".$column_name;
        return $this;
    }
    public function having($having_condition){
        $this->having="having ".$this->unique_key($having_condition);
        return $this;
    }
    public function get($is_refresh=false){
        $query_string="";
        foreach ($this->query_list as $key=>$value){
            $query_string.="$value,";
        }
        $sql="select $query_string from $this->table_name $this->join_ $this->where_ $this->group_by_ $this->having $this->limit_ $this->order_by_";
        $sql=str_replace("  "," ",$sql);
        $sql=str_replace(", "," ",$sql);
        $stm=$this->con->prepare($sql);//预处理 防止sql注入
        $this->bind_params($sql);
        $stm->setFetchMode(\PDO::FETCH_NAMED);
        $result = $stm->execute($this->bind_params($sql));
        if($is_refresh){
            $this->refresh();
        }
        if($result==false){
            return [];
        }
        if($result){
            $result=$stm->fetchAll();
            if(count($result)==1){
                return $result[0];
            }
        }
        $this->query_list=[];
        return $result;
    }
    public function get_table_column(){
        $result_arr=[];
        $sql="SHOW FULL COLUMNS FROM $this->table_name";
        $result=$this->con->query($sql);
        if(empty($result)){
            throw new \Exception('table '.$this->table_name." may not exist or columns not find");
        }
        foreach ($result as $row) {
            $result_arr[]=$row[0];
        }
        return $result_arr;
    }
    protected function pdo_count($pdo){
        $num=0;
        foreach ($pdo as $row){
            $num=$num+1;
        }
        return $num;
    }
    public function get_table_column_cache($is_refresh=false){
        $table_column=$this->cache()->get_cache($this->table_name."column");
        if($table_column==null||$is_refresh){
            $table_column=$this->get_table_column();
            $this->cache()->set_cache($this->table_name."column",$table_column,"forever");
        }
        return $table_column;
    }
    public function string($create_column_name,$length,$default_null=true,$primary_key=false){
        if($default_null===true){
            $default_null="default null";
        }
        else{
            $default_null="default '$default_null'";
        }
        if($primary_key==false){
            $primary_key="";
        }
        else{
            $primary_key="primary key";
            $default_null="default not null";
        }
        $this->create_table_column_list[]="$create_column_name varchar($length) $default_null $primary_key,";
        return $this;
    }
    public function unique(){
        $length=count($this->create_table_column_list);
        $this->create_table_column_list[$length-1]=str_replace(",,","",$this->create_table_column_list[$length-1].",unique,");
        return $this;
    }
    public function commemt($comment){
        $length=count($this->create_table_column_list);
        $this->create_table_column_list[$length-1]=str_replace(' ,',' ',str_replace(",,","",$this->create_table_column_list[$length-1]."comment '$comment',"));
        return $this;
    }
    public function char($create_column_name,$length,$default_null=true,$primary_key=false){
        if($default_null===true){
            $default_null="default null";
        }
        else{
            $default_null="default '$default_null'";
        }
        if($primary_key=false){
            $primary_key="";
        }
        else{
            $primary_key="primary key";
            $default_null="default not null";
        }
        $this->create_table_column_list[]="$create_column_name char($length) $default_null $primary_key,";
        return $this;
    }
    public function datetime($create_column_name){
        $this->create_table_column_list[]="$create_column_name DATETIME,";
        return $this;
    }
    public function integer($create_column_name,$length,$default_null=false,$primary_key=false,$auto_increment=false){
        if($default_null===false){
            $default_null="default null";
        }
        else{
            $default_null="default '$default_null'";
        }
        if($primary_key==false){
            $primary_key="";
        }
        else{
            $primary_key="primary key";
            $default_null="default not null";
        }
        if($auto_increment!=false){
            $auto_increment="auto_increment";
        }
        $this->create_table_column_list[]="$create_column_name int($length) $default_null $primary_key $auto_increment,";
        return $this;
    }
    public function tinyint($create_column_name,$length,$default_null=false,$primary_key=false,$auto_increment=false){
        if($default_null===false){
            $default_null="default null";
        }
        else{
            $default_null="default '$default_null'";
        }
        if($primary_key==false){
            $primary_key="";
        }
        else{
            $primary_key="primary key";
            $default_null="default not null";
        }
        if($auto_increment!=false){
            $auto_increment="auto_increment";
        }
        $this->create_table_column_list[]="$create_column_name tinyint($length) $default_null $primary_key $auto_increment,";
        return $this;
    }
    public function unsignedinteger($create_column_name,$length,$default_null=false,$primary_key=false,$auto_increment=false){
        if($default_null===false){
            $default_null="default null";
        }
        else{
            $default_null="default '$default_null'";
        }
        if($primary_key==false){
            $primary_key="";
        }
        else{
            $primary_key="primary key";
            $default_null="default not null";
        }
        if($auto_increment!=false){
            $auto_increment="auto_increment";
        }
        $this->create_table_column_list[]="$create_column_name int($length) unsigned $default_null $primary_key $auto_increment,";
        return $this;
    }
    public function timestamp($create_column_name){
        $this->create_table_column_list[]="$create_column_name TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,";
        return $this;
    }
    public function text($create_column_name,$default_null=true){
        if($default_null===true){
            $default_null="default null";
        }
        else{
            $default_null="default '$default_null'";
        }
        $this->create_table_column_list[]="$create_column_name text $default_null,";
        return $this;
    }
    public function decimal($create_column_name,$length,$precison,$default_null=true){
        if($default_null===true){
            $default_null="default null";
        }
        else{
            $default_null="default '$default_null'";
        }
        $this->create_table_column_list[]="$create_column_name decimal($length,$precison) $default_null,";
        return $this;
    }
    public function foreign_key($this_table_key,$foreign_table,$foreign_key){
        $this->create_table_column_list[]="foreign key($this_table_key) references $foreign_table($foreign_key),";
        return $this;
    }
    public function create(){
//        $sql="create table if not exists $this->table_name(";
        $sql="create table $this->table_name(";
        foreach ($this->create_table_column_list as $value){
            $sql.=$value ;
        }
        $sql.=") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $sql=str_replace("  "," ",$sql);
        $sql=str_replace(",)",")",$sql);
        $sql=str_replace("default not null","not null",$sql);
        $sql=str_replace(",comment"," comment",$sql);
        echo $sql.PHP_EOL;
        $result=$this->con->exec($sql);
        if(is_numeric($result)){
            echo $sql.PHP_EOL;
            return true;
        }
        return false;
    }
    public function set($set_column_name,$set_value){
        $this->set_list[$set_column_name]=$set_value;
        return $this;
    }
    public function get_primary_key(){
        $table_name=$this->table_name;
        $databese_name=$this->databse_name;
        $sql="SELECT `COLUMN_NAME` FROM `information_schema`.`COLUMNS` WHERE (`TABLE_SCHEMA` = '$databese_name')AND (`TABLE_NAME` = '$table_name')AND (`COLUMN_KEY` = 'PRI')";
        $result=$this->con->query($sql);
        foreach ($result as $row) {
            return $row["COLUMN_NAME"];
        }
        return null;
    }
    public function update(){
        $sql="update $this->table_name set ";
        $set_string="";
        foreach ($this->set_list as $key=>$value){
            $set_string.="$key=".$this->unique_key($value).',';
        }
        $sql.="$set_string $this->where_";
        $sql=str_replace("  "," ",$sql);
        $sql=str_replace(", "," ",$sql);
        $stm=$this->con->prepare($sql);
        $result=$stm->execute($this->bind_params($sql));
        $this->refresh();
        return $result;
    }
    public function update_table_filed(){
        $add_column=[];
        $create_table_column=[];
        foreach ($this->create_table_column_list as $value){
            $create_table_column[substr($value,0,strpos($value," "))]=$value;
        }
        if(isset($create_table_column["foreign"])) {
            unset($create_table_column["foreign"]);
        }
        $diff=array_diff(array_keys($create_table_column),$this->get_table_column());
        if(empty($diff)){
            return false;
        }
        $table_name=$this->table_name;
        foreach ($diff as $key){
            $sql = "ALTER TABLE $table_name ADD $create_table_column[$key]";
            $sql = str_replace(" ,", ";", $sql);
            $sql = str_replace(",", ";", $sql);
            echo $sql.PHP_EOL;
            if(!$this->con->exec($sql)){
                return false;
            }
        }
        return true;
    }
    protected function drop(){
        $sql="drop table $this->table_name";
        $result=$this->con->exec($sql);
        if(is_numeric($result)){
            return true;
        }
        return false;
    }
    public function get_table_struct(){
        $return_result=[];
        $query_filed=["Type","Null","Key","Default"];
        $result=$this->con->query("desc $this->table_name");
        foreach ($result as $row) {
            foreach ($query_filed as $key){
                $return_result[$row["Field"]][]=[strtolower($key)=>$row[$key]];
            }
        }
        return $return_result;
    }
    public function __call($name, $arguments)
    {
        switch ($name){
            case "drop":
                return $this->drop();
                break;
            default:
                new Exception("404","call fun error drop");
                break;
        }
    }
    public function update_many($filed_name,array $update_arrs){
        $change_arr=[];
        $where_arr=[];
        if ($this->is_1_array($update_arrs)){
            $update_arrs=[$update_arrs];
        }
        foreach ($update_arrs as $update_arr){
            if(!array_key_exists($filed_name,$update_arr)){
                new Exception(400,"reqiured_$filed_name but_null_given");
            }
            foreach ($update_arr as $key=>$value){
                if($key==$filed_name){
                    $where_arr[]=$value;
                    continue;
                }
                if(!isset($change_arr[$key])){
                    $change_arr[$key]=[];
                }
                $change_arr[$key][]=[$update_arr[$filed_name]=>$value];
            }
        }
        $sql="update $this->table_name ";
        $is_frist=true;
        foreach ($change_arr as $key=>$values){
            if($is_frist) {
                $sql .= "set $key=case $filed_name ";
                $is_frist=false;
            }
            else{
                $sql .= "$key=case $filed_name ";
            }
            foreach ($values as $value1){
                foreach ($value1 as $key1=>$value) {
                    $sql .= "when '$key1' then '$value' ";
                }
            }
            $sql.="end,";
        }
        $sql=substr($sql,0,strlen($sql)-1);
        $sql.=" where $filed_name in(";
        foreach ($where_arr as $value){
            $sql.="'$value',";
        }
        $sql=substr($sql,0,strlen($sql)-1);
        $sql.=")";
        if(($where_string=$this->where_!="")){
            $where_string=str_replace("where","",$where_string);
            $sql.=$where_string;
        }
        return $sql;
    }
    //更新多行多字段
    //useage update_many("id",["id"=>2,"name"=>"赵李杰"，"sex"=>"woman"])
    public function get_insert_id(){
        return $this->con->lastInsertId();
    }
    protected function unique_key($value){
        $key=":a".strval(count($this->bind));
        $this->bind[$key]=$value;
        return $key;
    }
    protected function bind_params($sql){
        $bind_params=[];
        preg_match_all("/:a(\d)+/",$sql,$matchs);
        foreach ($matchs[0] as $item){
            $bind_params[$item]=$this->bind[$item];
        }
        return $bind_params;
    }
    public function __sleep(){
        $this->con=null;
        return array_keys(get_object_vars($this));
    }
    public function __wakeup(){
        $this->init();
    }
}