<?php

namespace db\model\native;
use db\model\model;
use PhpParser\Error;
use system\common;
use system\Exception;
use system\kernel\facede;

class follow extends model
{
    protected $table_name="follow";

    /**
     * @description 获取我当前的关注的人员
     * @param string $uid
     * @return array|null
     */
    protected function get_my_follows(string $uid){
        return $this->where("uid",$uid)
            ->select(['uid','follow_uid','id'])
            ->all();
    }

    /**
     * @description 获取关注我的人的列表
     * @param string $uid
     * @return array|null
     */
    protected function get_follows_me(string $uid){
        return $this->where('follow_uid',$uid)
            ->select(['uid','follow_uid','id'])
            ->all();
    }

    /**
     * @description 获取关注的信息 关注-相互关注
     * @param string $uid
     */
    public function get_my_follows_info(string $uid){
        $follow_other=common::array_group_by_key($this->get_my_follows($uid),'follow_uid');
        $follow_me=common::array_group_by_key($this->get_follows_me($uid),'uid');
        $result=[];
        foreach ($follow_me as $key=>$value){
            $is_follow_each=false;
            if(array_key_exists($key,$follow_other)){
                $is_follow_each=true;
            }
            $result[]=['id'=>$value['id'],'uid'=>$key,'is_follow_each'=>$is_follow_each];
        }
        return $result;
    }

    /**
     * @description 获取我关注的人信息-是否相互关注
     * @param string $uid
     */
    public function get_follow_me_info(string $uid){
        $follow_other=common::array_group_by_key($this->get_my_follows($uid),'follow_uid');
        $follow_me=common::array_group_by_key($this->get_follows_me($uid),'uid');
        $result=[];
        foreach ($follow_other as $key=>$value){
            $is_follow_each=false;
            if(array_key_exists($key,$follow_me)){
                $is_follow_each=true;
            }
            $result[]=['id'=>$value['id'],'uid'=>$key,'is_follow_each'=>$is_follow_each];
        }
        return $result;
    }

    /**
     * @description 关注人
     * @param string $uid
     * @param string $follow_uid
     * @return bool
     */
    public function add_follow(string $uid,string $follow_uid){
        $unique_id=md5($uid.$follow_uid);
        try {
            return $this->create([
                'unique_id_'=>$unique_id,
                'uid'=>$uid,
                'follow_uid'=>$follow_uid
            ]);
        }
        catch (\Exception $exception){
            return false;
        }
    }
    public function remove_follow(string $id,string $uid){
        return $this->where('uid',$id)
            ->where("follow_uid",$uid)
            ->delete();
    }

    /**
     * @description 获取一个用户的关注人数与粉丝数量
     * @param string $user_id
     * @return array
     */
    public function get_follow_and_fans_num(string $user_id){
        $this->refresh();
        $follow_num=$this
            ->where("follow_uid",$user_id)
            ->count(true);
        $fans_num=$this
            ->where("uid",$user_id)
            ->count(true);
        return ['follows'=>$follow_num,"fans"=>$fans_num];
    }

    /**
     * @description 查看某个人是否关注另外一个人
     * @param string $uid
     * @param string $follow_uid
     * @return bool
     */
    public function is_follow(string $uid,string $follow_uid){
        $this->refresh();
        return $this
            ->where("unique_id_",md5($uid.$follow_uid))
            ->exist(true);
    }
}