<?php

/**
 * @Description It is a router of mysql
 * In development we use master and slave structure or colony strucure
 * we usually use middleware to solve write and read problem which node we should connet
 */
namespace db\factory;

class SqlRouter
{
    //某个表对应的数据库如对数据一致性要求相对严格我们获取优先向这个对应的主库进行读
    protected $table_map=[
    ];
    protected $fail_nodes=[];//此处设置失败的节点
    //连接配置文件
    protected $node_config=[
    ];
    //连接对象池
    protected $connect_pool=["R"=>[],"W"=>[]];
    protected static $object;
    protected $redis;//redis 连接对象
    protected $path;//文件的路径
    public static $sql_router_key="SqlRouterRequest";//在redis中存储每个db的访问数量
    public static $fail_node="SqlNodeFailRequest";//在此处配置失败节点的信息
    protected $max_fail_times;//最大的失败次数
    protected $max_fail_per=0.3;//配置最大的失败节点比例
    protected $one_node_max_fail_times=5;//配置单个节点的最大失败次数超过后该节点不会连接改mysql实例
    protected $enable_check_num=5;//开启多节点检查数量
    /**
     * @description 添加连接配置
     * @param string $ip
     * @param string $port
     * @param string $database
     * @param string $user
     * @param string $password
     * @param string $type 改对象的连接类型-R=>"读库"|"R"=>"写库"
     * @param string $driver mysql|sqlserver|
     */
    public function Addconfig(string $ip,string $port,string $database,string $user,string $password,string $type="R",string $driver="mysql"){
        $rules=["R","w"];
        if(!in_array($type,$rules)){
            throw new \Exception("Undefined type $type accept-".implode("|",$rules));
        }
        if(!isset($this->node_config[$type])){
            $this->connect_pool[$type]=[];
        }
        $this->node_config[$type][]=[
            "ip"=>$ip,
            "port"=>$port,
            "password"=>$password,
            "database"=>$database,
            "user"=>$user,
            "driver"=>$driver
        ];
    }
    protected function __construct(string $path,\Redis $redis)
    {
        $this->path=$path;
        $this->redis=$redis;
        $this->fail_nodes=$this->redis->hGetAll(self::$fail_node);
        foreach ($this->fail_nodes as &$fail_node){
            $fail_node=json_decode($fail_node,true);
        }
        $this->init();
    }

    /**
     * @Description 获取本机IP地址
     * @return mixed
     */
    public function get_server_ips(){
        if(preg_match("/cli/i", php_sapi_name()) ? true : false) {
            exec('ifconfig -a|grep inet|grep -v 127.0.0.1|grep -v inet6|awk \'{print $2}\'|tr -d "addr:"', $arr);
            return $arr[0];
        }else{
            return $_SERVER['SERVER_ADDR'];
        }
    }
    public static function SingleTon(string $path,\Redis $redis){
        if(self::$object==null){
            self::$object=new self($path,$redis);
        }
        return self::$object;
    }

    /**
     * @description 判断是读操作还是写的操作
     * @param string $sql
     * @return string
     */
    protected function CheckMode(string $sql){
        $sql=trim(strtolower($sql));
        if(preg_match("/(select|show)(.*?)/",$sql)){
            return "R";
        }else{
            return "W";
        }
    }

    /**
     * @description 获取连接
     * @param array $config
     * @return mixed|\PDO
     */
    protected function Connection(array $config,string $mode="R"){
        $unque_id=md5(json_encode($config));
        if(array_key_exists($unque_id,array_merge($this->connect_pool["R"]))){
            return $this->connect_pool["R"][$unque_id];
        }
        if(array_key_exists($unque_id,array_merge($this->connect_pool["W"]))){
            return $this->connect_pool["W"][$unque_id];
        }
        $user = $config["user"];
        $password =$config["password"];
        $database =$config["database"];
        $host = $config['ip'];
        $port=$config['port'];
        $dsn = "mysql".":host=$host;port=$port;dbname=$database;charset=utf8";
        try {
            $con= new \PDO($dsn, $user, $password);
            $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connect_pool[$mode][$unque_id] =$con;
            return $con;
        }
        catch (\Exception $exception){
            $this->FailConnectionHandle($config);
            foreach ($this->node_config[$mode] as $key=>$item){
                if($unque_id==md5(json_encode($item))){
                    unset($this->node_config[$mode][$key]);
                    if(empty($this->node_config[$mode])) {
                        throw new \Exception("No useful node please recover the Node of $mode");
                    }
                }
            }
            return $this->GetConnection($mode);
        }
    }

    /**
     * @description 获取连接对象
     * @param string $mode
     * @return mixed|\PDO
     * @throws \Exception
     */
    public function GetConnection(string $mode){
        $rules=['R',"W"];
        if(!in_array($mode,$rules)) {
            throw new \Exception("Undefined type $mode accept-" . implode("|", $rules));
        }
        if(empty($this->connect_pool[$mode])){
            $config_list=[];
            foreach ($this->node_config[$mode] as $item){
                $config_list[$this->ConfigToString($item)]=$item;
            }
            $select_unique_id=$this->balance($this->BlockFailNode(array_keys($config_list)));
            if(empty($select_unique_id)){
                throw new \Exception("No useful node please recover the Node of $mode");
            }
            $node=$config_list[$select_unique_id];
            return $this->Connection($node,$mode);
        }else{
            return reset($this->connect_pool[$mode]);
        }
    }
    public function __call($name, $arguments)
    {
        switch ($name){
            case "query":
                return call_user_func_array([$this->GetConnection("R"),'query'],$arguments);
                break;
            case "prepare":
                $mode=$this->CheckMode($arguments[0]);
                return call_user_func_array([$this->GetConnection($mode),'prepare'],$arguments);
                break;
            default:
                return call_user_func_array([$this->GetConnection($this->CheckMode($arguments[0]??'')),$name],$arguments);
                break;
        }
    }

    /**
     * @description 负载均衡调度器-mysql-redis
     * @param array $nodes
     * @return false|string
     */
    protected function balance(array $nodes){
        $script=<<<EOT
        local result=redis.call('get',KEYS[1]);
        local ip_adress_list=cjson.decode(KEYS[2])
        if result==nil or result==false then
           result={}
           for key,value in pairs(ip_adress_list) do
               result[value]=0 
           end
           result[ip_adress_list[1]]=1 
           redis.call('set',KEYS[1],cjson.encode(result))
           return ip_adress_list[1]
        else
            result=cjson.decode(result)
            if result[ip_adress_list[1]]==nil then
               result[ip_adress_list[1]]=0
            end
            local min_key=ip_adress_list[1]
            local min=result[ip_adress_list[1]]
            for key,value in pairs(ip_adress_list) do
                if result[value]==nil then
                   result[value]=0
                end
                if (result[value]<min) then
                   min=result[value]
                   min_key=value
                end
            end
            result[min_key]=result[min_key]+1
            redis.call('set',KEYS[1],cjson.encode(result))
            return min_key
        end
EOT;
        return $this->redis->eval($script,[self::$sql_router_key,json_encode(array_unique($nodes))],2);
    }

    /**
     * @description 生成配置文件
     * @param string $path
     * @throws \Exception
     */
    protected function init_config(string $path){
        $ReadMe="/*
*@description it is a sql-router config file use it to change config file
*@R just read-only cluster
*@W read-write it is a master
*@description it file should be
*example {
                 \"ip\":\"192.168.0.1\",
                 \"user\":\"root\",
                 \"port\":3306,
                 \"database\":\"cluster-1\",
                 \"password\":\"123789\"
               }
*/";
        $config="{
  \"R\":[
 {
        \"ip\":\"192.168.0.1\",
        \"user\":\"root\",
        \"port\":3306,
        \"database\":\"cluster-1\",
        \"password\":\"123789\"
      }
    ],
    \"W\":[
      
    ]
}";
        if(!file_put_contents($path."/"."SqlRouterConfig.json",$config)){
            throw new \Exception("Fail to create SqlRouterConfig.json");
        }
        file_put_contents($path."/ReadMe-SqlRouter.txt",$ReadMe);
    }
    protected function init(){
        if(!is_file($this->path.'/SqlRouterConfig.json')){
            $this->init_config($this->path);
            throw new \Exception("Config File has been created and located at ".$this->path.'/SqlRouterConfig.json');
        }else{
            $config=json_decode(file_get_contents($this->path."/SqlRouterConfig.json"),true);
            if(empty($config['R'])){
                throw new \Exception("fail to read Read-Cluster node please configurate it");
            } if(empty($config['R'])){
                throw new \Exception("fail to read Read-Cluster node please configurate it");
            }
            if(empty($config['W'])){
                throw new \Exception("fail to read Master node please configurate it");
            }
            $this->node_config=$config;
            $this->max_fail_times=count($this->node_config["R"]+$this->node_config["W"])*$this->max_fail_per;
        }
    }

    /**
     * @description 节点连接失败的处理方式
     * @param array $config
     */
    protected function FailConnectionHandle(array $config){
        if(empty($this->node_config['ServerIp'])){
            $ip=$this->get_server_ips();
            $this->node_config['ServerIp']=$ip;
            file_put_contents($this->path."/"."SqlRouterConfig.json",json_encode($this->node_config));
        }else{
            $ip=$this->node_config['ServerIp'];
        }
        $unique_id=$this->ConfigToString($config);
        $script=<<<EOT
        local result=redis.call('hGet',KEYS[1],KEYS[2]);
        if result==nil or result==false then
           local list={}
           list[ARGV[1]]=1
           return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(list))
        else
           result=cjson.decode(result)
           local num=result[ARGV[1]]
           if num==nil then
              num=0
           end
           result[ARGV[1]]=num+1
           return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result))
        end
EOT;
        $this->redis->eval($script,[self::$fail_node,$unique_id,$ip],2);
        if(!array_key_exists($ip,$this->fail_nodes)){
            $this->fail_nodes[$ip]=0;
        }
        $this->fail_nodes[$ip]+=1;
    }

    /**
     * @description 暂时屏蔽失败的mysql节点
     * @param array $UniqueId_list
     */
    protected function BlockFailNode(array $NodeConfigList){
        $node_num=count($this->node_config["R"])+count($this->node_config["W"]);
        foreach ($NodeConfigList as $key=>$value) {
            if(!$this->CheckIsOk($value)){
                unset($NodeConfigList[$key]);
            }
        }
        return array_values($NodeConfigList);
    }

    /**
     * @description 输出失败的节点
     * @return array
     */
    public function GetFailNodeInfo(){
        return $this->fail_nodes;
    }

    /**
     * @description 检查状态是否可用
     * @param string $unique_id
     * @return bool
     */
    protected function CheckIsOk(string $unique_id){
        $value=$unique_id;
        if (array_key_exists($value, $this->fail_nodes)) {
            $node_num = count($this->node_config["R"]) + count($this->node_config["W"]);
            if ($node_num > $this->enable_check_num && count($this->fail_nodes[$value]) > $this->max_fail_per * $node_num) {
                return false;
            }
            if (isset($this->fail_nodes[$value][$this->node_config['ServerIp']]) && $this->fail_nodes[$value][$this->node_config['ServerIp']] >= $this->one_node_max_fail_times) {
                return false;
            }
        }
        return true;
    }
    public function GetNodeRequestInfo(){
        $node_info=$this->node_config;
        $node_request_info=json_decode($this->redis->get(self::$sql_router_key),true);
        foreach ($node_info["W"] as &$item){
            $unqie_id=$this->ConfigToString($item);
            $item['unique_id']=$unqie_id;
            if(isset($node_request_info[$unqie_id])){
                $item['request']=$node_request_info[$unqie_id];
            }else{
                $item['request']=0;
            }
            if(isset($this->fail_nodes[$unqie_id])){
                $item['fail']=$this->fail_nodes[$unqie_id];
            }else{
                $item['fail']=0;
            }
            $item['status']=$this->CheckIsOk($unqie_id);
        }
        foreach ($node_info["R"] as &$item){
            $unqie_id=$this->ConfigToString($item);
            $item['unique_id']=$unqie_id;
            if(isset($node_request_info[$unqie_id])){
                $item['request']=$node_request_info[$unqie_id];
            }else{
                $item['request']=0;
            }
            if(isset($this->fail_nodes[$unqie_id])){
                $item['fail']=$this->fail_nodes[$unqie_id];
            }else{
                $item['fail']=0;
            }
            $item['status']=$this->CheckIsOk($unqie_id);
        }
        return $node_info;
    }
    /**
     * @description 尝试恢复节点
     */
    public function Recover(string $NodeID){
        $config=[];
        foreach (array_merge($this->node_config["R"],$this->node_config["W"]) as $item){
            if($NodeID==$this->ConfigToString($item)){
                $config=$item;
                continue;
            }
        }
        if(empty($config)){
            throw new \Exception("fail find node may node info has been changed");
        }
        $user = $config["user"];
        $password =$config["password"];
        $database =$config["database"];
        $host = $config['ip'];
        $dsn = "mysql".":host=$host;dbname=$database;charset=utf8";
        try {
            $con= new \PDO($dsn, $user, $password);
            $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return  $this->redis->hDel(self::$fail_node,$NodeID);
        }
        catch (\Exception $exception){
            $this->FailConnectionHandle($config);
            throw new \Exception($exception->getMessage());
        }
    }
    /**
     * @description 获取连接的唯一id
     * @param array $config
     * @return string
     */
    protected function ConfigToString(array $config){
        return $config['ip'].'-'.$config['port'].md5(json_encode($config));
    }
}