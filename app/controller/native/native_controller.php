<?php
/**
 * Created by awesome.
 * Date: 2019-10-10 05:01:31
 */
namespace app\controller\native;
use app\controller\auth\auth_controller;
use app\controller\controller;
use request\request;
use system\class_define;
use system\common;
use system\config\config;
use system\Exception;
use system\session;

class native_controller extends controller
{
    protected $type=[
        "全部","王者荣耀", "英雄联盟", "和平精英", "星秀", "其他直播", "绝地求生", "lol云顶之弈", "交友", "颜值", "户外",
        "魔兽世界", "穿越火线", "其他手游", "地下城与勇士", "CF手游", "王者模拟战", "二次元", "QQ飞车手游", "逆战", "跑跑卡丁车手游",
        "我的世界", "主机游戏", "球球大作战", "梦三国", "QQ飞车", "魔兽争霸3", "体育", "问道手游", "CS:GO", "坦克世界",
        "多多自走棋", "英魂之刃", "一起看", "王牌战争：文明重启", "火影忍者手游", "明日之后", "创造与魔法", "音乐", "剑灵", "美食"
    ];
    protected $online_list_key=[
        "game",
        "beautiful",
        "lol",
        "eat_chicken",
        "wangzherongyao",
        "dnf",
        "games",
        "cf",
        "dota2",
        "hepingjingying",
        "more"
    ];
    protected $record_push_url="record_native_push_url";
    public function start(request $request){
        $user_id=auth_controller::auth();
        $online=$this->online_list_key;
        foreach ($online as $key=>$item){
            $online[$key]="online_".$item;
        }
        $online_string=implode(",",$online);
        $rules=[
            "type"=>"required:get|$online_string"
        ];
        $request->verifacation($rules);
        $redis=class_define::redis();
        $room_num=common::rand(6,"number");
        while ($redis->hExists($request->get("type"),$room_num)){
            $room_num=common::rand(6,"number");
        }
        $cover=$request->get_file("cover");
        if($cover->isset_file()){
            $cover_path=$cover->accept(["jpg", "png", "jpeg", "gif","image/*"])->max_size(2024)->store_upload_file(config::env_path()."public/image/upload");
        }
        else{
            new Exception("404","cover_path_not_find");
        }
        $rtmp_url=$this->getPushUrl();
        $redis->hSet($this->record_push_url,$rtmp_url['stream_name'],json_encode(["room"=>$room_num,"type"=>$request->get("type")]));//记录流名与房间名的映射关系
        $native_info=[
            "user_id"=>$user_id,
            "rtmp_url"=>$rtmp_url['push'],
            "play"=>$rtmp_url["play"],
            "crated_at"=>$this->time(),
            "user_name"=>session::get("name"),
            "room"=>$room_num,
            "cover"=>$cover_path
        ];
        $redis->hSet($request->get("type"),$room_num,json_encode($native_info));
        return $native_info;
    }
    protected function start_native(array $native_view_message){

    }
    protected function level_native(){

    }
    public function getPushUrl(){
        $app_name="titang_native_app";
        $domain="63817.livepush.myqcloud.com";
        $play_domain="play.titang.shop";
        $streamName=md5(common::rand(8).microtime(true));
        $key="secret";
        $time=date('Y-m-d H:i:s',strtotime("+1 day"));
        if($key && $time){
            $txTime = strtoupper(base_convert(strtotime($time),10,16));
            //txSecret = MD5( KEY + streamName + txTime )
            $txSecret = md5($key.$streamName.$txTime);
            $ext_str = "?".http_build_query([
                    "txSecret"=> $txSecret,
                    "txTime"=> $txTime
                ]);
        }
        return [
            "push"=> "rtmp://".$domain."/live/".$streamName . (isset($ext_str) ? $ext_str : ""),
            "play"=>"http://$play_domain/$app_name/$streamName.flv".(isset($ext_str) ? $ext_str : ""),
            "stream_name"=>$streamName
            ];
    }
    public function get_native_type(request $request){
        $native_type=[
            [
                "name"=>"颜值",
                "url"=>"https://ss1.bdstatic.com/70cFvXSh_Q1YnxGkpoWK1HF6hhy/it/u=2615516231,2905958076&fm=26&gp=0.jpg",
                "type"=>"beautiful",
            ],
            [
                "name"=>"英雄联盟",
                "url"=>"https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2337237933,2000847306&fm=26&gp=0.jpg",
                "type"=>"lol",
            ],
            [
                "name"=>"绝地求生",
                "url"=>"https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2110681502,1535098269&fm=26&gp=0.jpg",
                "type"=>"eat_chicken",
            ],
            [
                "name"=>"王者荣耀",
                "url"=>"https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=202548807,2558761829&fm=26&gp=0.jpg",
                "type"=>"wangzherongyao",
            ],
            [
                "name"=>"DNF",
                "url"=>"https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1740738040,1113126080&fm=26&gp=0.jpg",
                "type"=>"dnf",
            ],
            [
                "name"=>"主机游戏",
                "url"=>"https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2779191552,1421813831&fm=26&gp=0.jpg",
                "type"=>"games",
            ],
            [
                "name"=>"穿越火线",
                "url"=>"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1571160934976&di=331d2828b4b328f7757d8a02841329d5&imgtype=0&src=http%3A%2F%2Fkascdn.kascend.com%2Fjellyfish%2Fspace%2Ftopic%2F170203%2F1486114235309.jpg",
                "type"=>"cf",
            ],
            [
                "name"=>"和平精英",
                "url"=>"https://ss0.baidu.com/6ONWsjip0QIZ8tyhnq/it/u=3460768464,3742418663&fm=58&bpow=400&bpoh=400",
                "type"=>"hepingjingying"
            ],
            [
                "name"=>"DOTA",
                "url"=>"https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=3134454663,859220742&fm=26&gp=0.jpg",
                "type"=>"dota2"
            ],
//            [
//                "name"=>"更多",
//                "url"=>"https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=2489554980,842697378&fm=26&gp=0.jpg",
//                "type"=>"more"
//            ],
        ];
        if($request->try_get("version")){
            return ["code"=>200,"version"=>md5(json_encode($native_type))];
        }
        return ["code"=>200,"data"=>$native_type,"version"=>md5(json_encode($native_type))];
    }
    public function get_online_list(tencent_clound $tencent_clound,request $request){
//        return "[{\"room_name\":\"223673\",\"type\":\"dnf\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/d17cb2a89e69018021ece7c5770c3c37?txSecret=3ecd84150a3ea1c9c1703907e8a6f893&txTime=5DB786DC\",\"play\":\"http://5815.liveplay.myqcloud.com/live/5815_89aad37e06ff11e892905cb9018cf0d4_900.flv\",\"crated_at\":\"2019-10-28 08:25:00\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"223673\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/2a99a77a2bec648d914c853900bfba30.jpg\"}},
//        ,{\"room_name\":\"837937\",\"type\":\"lol\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/dfaf4f9bf8d011dcba1f9119c0347b7c?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/dfaf4f9bf8d011dcba1f9119c0347b7c.flv?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"crated_at\":\"2019-10-28 16:07:17\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"837937\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/8f7b40b953479a8ee0dc3547ea2f5b5f.jpg\"}}
//        ,{\"room_name\":\"837937\",\"type\":\"lol\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/dfaf4f9bf8d011dcba1f9119c0347b7c?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/dfaf4f9bf8d011dcba1f9119c0347b7c.flv?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"crated_at\":\"2019-10-28 16:07:17\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"837937\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/8f7b40b953479a8ee0dc3547ea2f5b5f.jpg\"}}
//        ,{\"room_name\":\"837937\",\"type\":\"lol\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/dfaf4f9bf8d011dcba1f9119c0347b7c?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/dfaf4f9bf8d011dcba1f9119c0347b7c.flv?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"crated_at\":\"2019-10-28 16:07:17\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"837937\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/8f7b40b953479a8ee0dc3547ea2f5b5f.jpg\"}}
//        ,{\"room_name\":\"837937\",\"type\":\"lol\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/dfaf4f9bf8d011dcba1f9119c0347b7c?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/dfaf4f9bf8d011dcba1f9119c0347b7c.flv?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"crated_at\":\"2019-10-28 16:07:17\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"837937\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/8f7b40b953479a8ee0dc3547ea2f5b5f.jpg\"}}
//        ,{\"room_name\":\"837937\",\"type\":\"lol\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/dfaf4f9bf8d011dcba1f9119c0347b7c?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/dfaf4f9bf8d011dcba1f9119c0347b7c.flv?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"crated_at\":\"2019-10-28 16:07:17\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"837937\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/8f7b40b953479a8ee0dc3547ea2f5b5f.jpg\"}}
//        ,{\"room_name\":\"837937\",\"type\":\"lol\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/dfaf4f9bf8d011dcba1f9119c0347b7c?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/dfaf4f9bf8d011dcba1f9119c0347b7c.flv?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"crated_at\":\"2019-10-28 16:07:17\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"837937\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/8f7b40b953479a8ee0dc3547ea2f5b5f.jpg\"}}
//        ,{\"room_name\":\"837937\",\"type\":\"lol\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/dfaf4f9bf8d011dcba1f9119c0347b7c?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/dfaf4f9bf8d011dcba1f9119c0347b7c.flv?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"crated_at\":\"2019-10-28 16:07:17\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"837937\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/8f7b40b953479a8ee0dc3547ea2f5b5f.jpg\"}}
//        ,{\"room_name\":\"837937\",\"type\":\"lol\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/dfaf4f9bf8d011dcba1f9119c0347b7c?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/dfaf4f9bf8d011dcba1f9119c0347b7c.flv?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"crated_at\":\"2019-10-28 16:07:17\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"837937\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/8f7b40b953479a8ee0dc3547ea2f5b5f.jpg\"}}
//        ,{\"room_name\":\"837937\",\"type\":\"lol\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/dfaf4f9bf8d011dcba1f9119c0347b7c?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/dfaf4f9bf8d011dcba1f9119c0347b7c.flv?txSecret=66819026734242f95cc633bac3261d61&txTime=5DB7F335\",\"crated_at\":\"2019-10-28 16:07:17\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"837937\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/8f7b40b953479a8ee0dc3547ea2f5b5f.jpg\"}}]";
        return "[{\"room_name\":\"387992\",\"type\":\"games\",\"info\":{\"user_id\":\"\u8d75\u674e\u6770\",\"rtmp_url\":\"rtmp:\/\/63817.livepush.myqcloud.com\/live\/5e89980b6c751ffeba54497178381624?txSecret=b42b1e336f50f0a7a95a6409044e0fb7&txTime=5DF732B6\",\"play\":\"http:\/\/play.titang.shop\/titang_native_app\/5e89980b6c751ffeba54497178381624.flv?txSecret=b42b1e336f50f0a7a95a6409044e0fb7&txTime=5DF732B6\",\"crated_at\":\"2019-12-15 15:31:02\",\"user_name\":\"\u8d75\u674e\u6770\",\"room\":\"387992\",\"cover\":\"http:\/\/www.titang.shop\/image\/upload\/9ac2308e1b13229fb9511afec4eacdf6.jpg\"}}]";
        $online_info=[];
        $redis=class_define::redis();
        $tencent_clound=new tencent_clound();
        foreach ($tencent_clound->get_online_list() as $value){
            $value=get_object_vars($value);
            $stream_info=json_decode($redis->hGet($this->record_push_url,$value['StreamName']),true);
            if(($type=$request->try_get("type"))==false) {
                $online_info[] = [
                    "room_name" => $stream_info['room'],
                    "type" => $stream_info['type'],
                    "info"=>json_decode($redis->hGet($stream_info['type'],$stream_info['room']),true)
                ];
            }
            else{
                $online_string=implode(",",$this->online_list_key);
                $rules=[
                    "type"=>"required:get|accept:$online_string"
                ];
                $request->verifacation($rules);
                if($type==$stream_info['type']){
                    $online_info[] = [
                        "room_name" => $stream_info['room'],
                        "type" => $stream_info['type'],
                        "info"=>json_decode($redis->hGet($request->get("type"),$stream_info['room']),true)
                    ];
                }
            }
        }
        return $online_info;
    }
    public function banner(){
        return [
            [
                "img"=>"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1572452937315&di=13170aecf0192f5996e17c947564387c&imgtype=0&src=http%3A%2F%2Fimage.namedq.com%2Fuploads%2F20181219%2F22%2F1545229622-TLOjqJABCH.jpg",
                "title"=>"王者荣耀"
            ],
            [
                "img"=>"https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=87066789,2271427905&fm=26&gp=0.jpg",
                "title"=>"LOL"
            ],
            [
                "img"=>"https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=870371043,1189525928&fm=26&gp=0.jpg",
                "title"=>"DNF"
            ],
            [
                "img"=>"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1573049005&di=45448d0f3c455461cab604296fa4f3b7&imgtype=jpg&er=1&src=http%3A%2F%2Fimg.zcool.cn%2Fcommunity%2F01f8d65bbb2489a8012099c810ed10.jpg%401280w_1l_2o_100sh.jpg",
                "title"=>"刺激战场"
            ],
            [
                "img"=>"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1573049005&di=45448d0f3c455461cab604296fa4f3b7&imgtype=jpg&er=1&src=http%3A%2F%2Fimg.zcool.cn%2Fcommunity%2F01f8d65bbb2489a8012099c810ed10.jpg%401280w_1l_2o_100sh.jpg",
                "title"=>"刺激战场"
            ]
        ];
    }
    public function type(){
        return $this->type;
    }
}
