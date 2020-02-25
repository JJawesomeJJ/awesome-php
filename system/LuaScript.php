<?php
/**
 * Description It is a redis tool class use Luascript to ensure atomicity
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/26 0026
 * Time: 下午 3:50
 * Author JJawesome
 */

namespace system;


class LuaScript
{
    /**
     * @description 将hash变量中的array中增加一条
     * @param $hash_key
     * @param $key_name
     * @param $value
     * @return mixed
     */
    public static function hash_add_array(string $hash_key,string $key_name,$value){
        if(!is_string($value)){
            $value=json_encode($value);
        }
        $script=<<<EOT
        local result=redis.call('hGet',KEYS[1],KEYS[2]);
        if result==nil or result==false then
           local list={ARGV[1]}
           return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(list))
        else
           result=cjson.decode(result)
           table.insert(result,ARGV[1])
           return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result))
        end
EOT;
        return class_define::redis()->eval($script,array($hash_key,$key_name,$value),2);
    }

    /**
     * @description 向hash中的json修改或者添加一个hash
     * @param string $hash_key 哈希的大键
     * @param string $key_name 哈希的小键
     * @param string $key json的键
     * @param string $value json的值
     * @return mixed
     */
    public static function hash_add_hash(string $hash_key,string $key_name,string $key,string $value){
        $script=<<<EOT
        local result=redis.call('hGet',KEYS[1],KEYS[2]);
        if result==nil or result==false then
            local list={};
            list[ARGV[1]]=ARGV[2]
            return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(list));
        else
            result=cjson.decode(result)
            result[ARGV[1]]=ARGV[2]
            return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result));
        end
EOT;
        return class_define::redis()->eval($script,func_get_args(),2);
    }

    /**
     * @description 移除hash中的某个键
     * @param string $hash_key
     * @param string $key_name
     * @param string $key
     */
    public static function hash_del_key(string $hash_key,string $key_name,string $key){
        $script=<<<EOP
        local result=redis.call('hGet',KEYS[1],KEYS[2])
        local keys1={}
        if result==nil or result==false then
            local list={};
            return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(list));
        else
            result=cjson.decode(result)
            result[ARGV[1]]=nil
            return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result));
        end
EOP;
        return class_define::redis()->eval($script,func_get_args(),2);
    }

    /**
     * @description 向json格式中的一个键里面添加一个key&value
     * @json={
     * "hash_key3":{}
     * }
     * @result={
     * "hash_key3":{hash_key4:value}
     * }
     * @param string $hash_key1
     * @param string $hash_key2
     * @param string $hash_key3
     * @param string $hash_key4
     * @param string $value
     * @return mixed
     */
    public static function hash_hash_add_key_value(string $hash_key1,string $hash_key2,string $hash_key3,string $hash_key4,string $value){
        $script=<<<EOP
        local result=redis.call('hGet',KEYS[1],KEYS[2])
        local keys1={}
        if result==nil or result==false then
            local list={};
            list[ARGV[1]]={}
            list[ARGV[1]][ARGV[2]]=ARGV[3]
            return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(list));
        else
            result=cjson.decode(result)
            if result[ARGV[1]]==nil then
               result[ARGV[1]]={}
            end
            result[ARGV[1]][ARGV[2]]=ARGV[3]
            return redis.call('hSet',KEYS[1],KEYS[2],cjson.encode(result));
        end
EOP;
        return class_define::redis()->eval($script,func_get_args(),2);
    }

    /**
     * @description 获取一个hash中数组的长度
     * @param string $hash_key
     * @param string $key_name
     * @return mixed
     */
    public static function hash_arr_len(string $hash_key,string $key_name){
        $script=<<<EOT
        local result=redis.call('hGet',KEYS[1],KEYS[2]);
        if result==nil or result==false then
           return 0;
        else
           result=cjson.decode(result)
           return table.getn(result)
        end
EOT;
        return class_define::redis()->eval($script,func_get_args(),2);
    }

    /**
     * @description 获取hash字段中的json的长度
     * @param string $hash_key
     * @param string $key_name
     * @return mixed
     */
    public static function hash_hash_len(string $hash_key,string $key_name){
        $script=<<<EOT
        local result=redis.call('hGet',KEYS[1],KEYS[2]);
        if result==nil or result==false then
           return 0;
        else
            result=cjson.decode(result)
            local len_=0;
            for key, value in pairs(result) do
            len_=len_+1; 
            end 
            return len_;
        end
EOT;
        return class_define::redis()->eval($script,func_get_args(),2);
    }

    /**
     * @description 用于负载均衡计算
     * @description 不过每一次有一个redis-io 在分布式中考虑增加一层redis集群
     * @description 获取数据中最小的访问次数且加一返回最小的key
     * @param string $key
     * @param array $arr
     * @return false|string
     */
    public static function string_hash_min_increase(string $key,array $arr){
        $placeholder="a_";
        foreach ($arr as &$value){
            $value=$placeholder.$value;
        }
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
        $result=class_define::redis()->eval($script,[$key,json_encode(array_unique($arr))],2);
        $placeholder_len=mb_strlen($placeholder);
        return mb_substr($result,$placeholder_len,mb_strlen($result)-$placeholder_len);
    }
}