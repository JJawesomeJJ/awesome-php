<?php
namespace system\cache\abstract_;

abstract class CacheDriver
{
    /**
     * 设置一个缓存
     * @param string $key 缓存的键
     * @param mixed $value 缓存的值
     * @param int|string $expire 过期时间
     * @return void
     */
    abstract public function set(string $key,$value,$expire="forever");

    /**
     * 获取一个缓存
     * @param string $key
     * @return mixed
     */
    abstract public function get(string $key);

    /**
     * 获取一个缓存如果不存在则设置这个缓存
     * @param string $key
     * @param \Closure $fun
     * @param $expire
     * @return mixed
     */
    abstract public function getorset(string $key,\Closure $fun,$expire="forever");

    /**
     * 检查某个缓存是是否存在
     * @param string $key
     * @return mixed
     */
    abstract public function exist(string $key):bool;

    /**
     * 为某个缓存设置一个锁
     * @param string $key
     * @param int $timeout 这个锁的最大时间
     * @param int $max_fail_timeout 尝试获取这个锁的最大时间
     * @return mixed
     * @throw Exception
     */
    abstract public function lock(string $key,int $timeout,int $max_fail_timeout);

    /**
     * 解锁某个锁
     * @param string $key 锁的名字
     * @return mixed
     */
    abstract public function unlock(string $key);

    /**
     * 让某个键的自增
     * @param string $key
     * @return int 返回设置后的值
     * @throws \Exception
     */
    abstract public function incrBy(string $key,$expire="forever",int $default=1,$incr_value=1);

    /**
     * 让某个键自动减少
     * @param string $key
     * @return int $default 返回设置后的值
     * @throws \Exception 当这个值不是数字的是丢出的异常
     */
    abstract public function decrBy(string $key,$expire="forever",int $default=1,$incr_value=1);

    /**
     * @param string $key
     * @return mixed
     */
    abstract function del(string $key);
    /**
     * 检查这个缓存是否过期
     * @param int $created_at 创建时期
     * @param string|int $expire_seconds 过期时间
     * @return bool
     */
    protected function is_expire(int $created_at,$expire_seconds):bool{
        if(is_string($expire_seconds)){
            return false;
        }else{
            if($created_at+$expire_seconds<=time()){
                return false;
            }
            else{
                return true;
            }
        }
    }

    /**
     * 获取所有的缓存以键值对存在
     * @return array
     */
    abstract function get_all():array;

    /**
     * 删除所有的缓存
     * @return mixed
     */
    abstract function del_all();
}