<?php
/**
 * Created by awesome.
 * Date: 2019-04-10 10:34:30
 */
namespace controller\index;
use controller\code\code_controller;
use controller\controller;
use db\db;
use db\factory\migration\migration;
use db\factory\migration\migration_list\migration_comment_list;
use db\factory\soft_db;
use db\model\comment_list\comment_list;
use db\model\model;
use db\model\model_auto\model_auto;
use db\model\park\map;
use db\model\shop\categories;
use db\model\shop\goods;
use db\model\user\user;
use load\auto_load;
use load\provider;
use load\provider_register;
use request\request;
use SebastianBergmann\CodeCoverage\Report\PHP;
use function Sodium\crypto_pwhash_scryptsalsa208sha256;
use SuperClosure\Serializer;
use system\cache\cache;
use system\cache\cache_;
use system\class_define;
use system\common;
use system\config\config;
use system\config\service_config;
use system\config\timed_task_config;
use system\cookie;
use system\encrypt;
use system\excel;
use system\file;
use system\http;
use system\mail;
use system\pay\alipay;
use system\redis;
use system\session;
use system\system_excu;
use system\upload_file;
use task\job\asyn_queue;
use task\queue\queue;
use task\rabbitmq;
use template\compile;
use template\compile_parse;

class index_controller extends controller
{
    public function index(request $request,user $user)
    {
//        return $this->get_user_ip();
//        $db=new db();
////        return $db->show_columns("user");
//          return soft_db::table("test11")
//              ->integer("id","5","not null",true,true)
//              ->text("vlog")
//              ->string("name","225","not null")
//              ->decimal("money","10","2")
//              ->timestamp("updated_at")
//              ->datetime("created_at")
//              ->create();
//        make("mail")->send_email("1293777844@qq.com","test","test")
//        return soft_db::table("comment_list")
//            ->join("user","name","user_id")
//            ->select("user_id","comment_content","head_img")
//            ->where("name","赵李杰","!=")
//            ->get();
//        $db=new db();
//        return $db->query(false,["table_name"=>["comment_list"=>["user_id","comment_content"],"user"=>["head_img"]],"link"=>["comment_list"=>"user_id","user"=>"name"]]);
//        soft_db::table("user")
//            ->where("name","赵李杰1")
//            ->where("email","1293777844@qq.com")
//            ->set("sex","man")
//            ->set("name","赵李杰")
//            ->update();
//        return soft_db::table("user")
//            ->all()
//            ->where("name","赵李杰")
//            ->get();
//
//        $user=new model();
//        return $user->id;
//
//        soft_db::table("user111")
//            ->string("name",20)
//            ->integer("user_id",20,true,true)
//            ->foreign_key("user_id","user11","id")
//            ->create();
//        $user=new model();
//        $user=$user->get();
//        $user->sex="man";
//        $user->update();
//        return $this->sex;
//        return microtime(true)-$GLOBALS["time"];
//        $model=new user();
//        $model->where("name","赵李杰")->get()->comment_list->comment_content="123";
//        var_dump($model->comment_list->time);
//        return microtime(true)-$GLOBALS["time"];
//        $redis=new \Redis();
//        $redis->connect("127.0.0.1", 6379);
//        while ($redis->lLen("emailfail")>0){
//            $data=unserialize($redis->lPop("emailfail"));
//            echo $data["fail"];
//            echo "</br>";
//        }
//        return;
//        return $redis->lLen("emailfail");
//        $redis->del("emailfail");
//        $queue=new queue();
//        $queue->push("email",["title"=>"欢迎注册","template"=>"login","user"=>"1293777844@qq.com","code"=>"sada"],"email");
//        return "ok";
//        $user1=new user();
//        $user1->where("name","赵李杰")->get();
//        $user1->sex="women";
//        $user1->update();
//        $user=new user();
//        $user->get();
//        $complie=new compile();
//        return $complie->view("test",$user->all());
//        $this->cache()->set_cache("sada","asd",10);
//        foreach ($this->cache()->get_all() as $value){
//            $value = json_decode($value, true);
//            if($value["expire"]!="forever") {
//                echo $value["key"] . date('Y-m-d H:i:s', $value["expire"]) . "<br>";
//            }
//            else{
//                echo $value["key"] .$value["expire"]. "<br>";
//            }
//        }
//    }
//        $user=new user();
//        return $user->get()->all();
//        $request=$this->request();
//        queue::asyn(function () use ($request){
//            echo "test";
//            $user=new user();
//            $user->where("name", $request->get("name"))->get();
//            $mail=new mail();
//            $mail->send_email($user->email,"test","test");
//        });
//        return microtime(true)-$GLOBALS["time"];
//        $compile=new compile();
//        $user=new user();
//        return $compile->view("test3",["user"=>$user->get()->all(),"head_img"=>"http://39.108.236.127/image/logo.png","name"=>"test"]);
//        $user=new user();
//        $user->where("name","赵李杰")->get();
//        $mail=new mail();
//        $mail->send_email($user->email,"test","test");
//        $this->cache()->delete_key(timed_task_config::timed_task_schedule()[0]);
//        //return $user->email;
//        return code_controller::code(10);
//        var_dump(system_excu::get_pid_php_script_name(4785));
//        return service_config::service_config();
//        $user_list=array_keys(class_define::redis()->hGetAll("user_list"));
//        $user=new user();
//        return $user->where_in("name",$user_list)->get()->all(["name","head_img"]);
//        view("test",["name"=>"赵李杰"]);
//        return microtime(true)-$GLOBALS["time"];
        //session::set("test","test");
//        return session::get("test");
//        $user=new user();
//        return $user->where("name","赵李杰")->get()->comment_list->comment_content;
//        $time = microtime(true);
//        view("admin/controller_index");
//        return microtime(true)-$GLOBALS["time"];
        //return var_export(file_get_contents(@"/var/www/html/php/template/test123.php"),true);
//        echo timed_task_config::timed_task_schedule()[0];
//        return $this->cache()->get_cache(timed_task_config::timed_task_schedule()[0]);
//        $this->cache()->set_cache("test","test",500);
//        return $this->cache()->get_cache("test");
//
//        session::set("test","jjawesome");
//
//        return $this->cache()->get_cache("sql");
//        print_r(soft_db::table("user")->get_table_struct());
//        $this->test();
//        $this->cache()->delete_key("de1fbbeb3f7bd001c15cd510ea808cb3");
//        return $this->cache()->get_cache("sql");
//        $user=new user();
//        print_r($user->where("name","赵李杰")->get()->all_with_foreign("comment_list"));
//        $map=new map();
//        $map->set_table_name("四川省")->where("city","乐山市")->get();
//        print_r($map->all_with_foreign("oder"));
//        echo microtime(true);
//        $file=new file();
//        print_r($file->file_walk("/var/www/html/php/extend/"));
//        $this->cache()->set_cache("test",5,16400);
//        $this->cache()->decrease("test",5);
//        return $this->cache()->get_cache("test");
//        $this->cache()->set_cache("test","123",6400);
//        return $this->cache()->pop("test");
//        var_dump(true==="dasd");
//        return config::swoole_dev();
//        return $this->request()->get_url();
//        print_r(get_included_files());
//        return $this->get_local_ip();
//        echo "load";
//        echo "ok";
//        echo "ok";
//        echo "reload";
//        $user=soft_db::table("user");
//        return $user->update_many("name",[
//            ["name"=>"赵李杰","password"=>hash("sha256","19971999"),"sex"=>"women"],
//            ["name"=>"潘泓达","password"=>hash("sha256","19971999"),"sex"=>"man"]
//        ]);
//        $user=new user();
////        $user->create([
////            "name"=>"小王",
////            "password"=>hash("sha256","19971998"),
////            "sex"=>"man",
////            "head_img"=>"test",
////            "email"=>"15255@qq.com"
////        ]);
//        $user->update(["password"=>"c7643438e12c101c2b4bf7a638a333e84a7b0125ea9ce28cd87a1f2542760e7e"]);
//        $comment_list=$user->where("name","赵李杰")->get()->comment_list();
//        $comment_list->where("comment_content","test")->get()->comment_content="tttttttttt";
//        $comment_list->update();
//        $user=make(user::class);
//        print_r($user->all_with_foreign("comment_list"));
//        $user=(new user())->where("name","赵李杰");
//        print_r($user->all_with_foreign("comment_list"));
//        return $this->cache()->get_cache("sql");
//        queue::asyn(function (){
//            $mail=new mail();
//            $mail->send_email("1293777844@qq.com","test",'tes');
////            $excel=new excel();
////            $cache=new cache();
////            $cache->set_cache("excel",$excel->read_excel("/var/www/html/php/grade.xlsx"),'12400');
//        });
//        $cache=new cache();
//        $cache->get_cache("excel");
//        $excel=new excel();
//        print_r($excel->read_excel("/var/www/html/php/grade.xlsx"));
//        echo microtime(true)-$GLOBALS["time"];
//        $cache=new cache();
//        return $cache->get_non_exist_set("user_status","test",'3600');
//        $redis=class_define::redis();
//        queue::asyn(function (){
//           $mail=new mail();
//           $compile=new compile();
//            $mail->send_email("1006954424@qq.com",$compile->view("tool/email",["code"=>code_controller::code(4),"title"=>"欢迎哥","content"=>'']),"测试");
//        });
//        class_define::redis();
//        $this->ajaxFileUplodeAction();
//        print_r($_SERVER);
//        $news=model_auto::model('news');
//        $news=$news->page($request->get('page'),$request->get('limit'),10,['content'],true);
//        print_r($news);
//        return view('news/news_list',['page'=>$news]);
//          $http=new http();
//        $response=$http->get('https://www.tmxiaoer.com/fd');
//        if(strpos($response,'系统错误')!==false){
//            $email=new \system\mail();
//            $email->send_email('1293777844@qq.com',$response,'server_error');
//        }
//          $brand=model_auto::model('brand_brand');
//          $data=$brand->where('getprice','<',100)->page($request->get('page',227),20);
//          $time=microtime(true);
//          return view('news/news_list',["page"=>$data]);
//        $categories = [
//            [
//                'name'     => '手机配件',
//                'children' => [
//                    ['name' => '手机壳'],
//                    ['name' => '贴膜'],
//                    ['name' => '存储卡'],
//                    ['name' => '数据线'],
//                    ['name' => '充电器'],
//                    [
//                        'name'     => '耳机',
//                        'children' => [
//                            ['name' => '有线耳机'],
//                            ['name' => '蓝牙耳机'],
//                        ],
//                    ],
//                ],
//            ],
//            [
//                'name'     => '电脑配件',
//                'children' => [
//                    ['name' => '显示器'],
//                    ['name' => '显卡'],
//                    ['name' => '内存'],
//                    ['name' => 'CPU'],
//                    ['name' => '主板'],
//                    ['name' => '硬盘'],
//                ],
//            ],
//            [
//                'name'     => '电脑整机',
//                'children' => [
//                    ['name' => '笔记本'],
//                    ['name' => '台式机'],
//                    ['name' => '平板电脑'],
//                    ['name' => '一体机'],
//                    ['name' => '服务器'],
//                    ['name' => '工作站'],
//                ],
//            ],
//            [
//                'name'     => '手机通讯',
//                'children' => [
//                    ['name' => '智能机',
//                        'children' => [
//                            ['name' => 'oppo'],
//                            ['name' => 'vivo'],
//                            ['name' => 'Apple'],
//                            ['name' => 'HUAWEI'],
//                            ['name' => 'MI',
//                                'children' => [
//                                    ['name' => 'redmi'],
//                                    ['name' => 'xiaomi'],
//                                ],
//                            ],
//                    ],
//                    ['name' => '老人机'],
//                    ['name' => '对讲机'],
//                ],
//            ],
//        ]];
//        $categories=json_decode($categories,true);
//        print_r($categories);
//        $categories_model=new categories();
//        $categories_model->delete();
//        $categories_model->add_categories($categories);
//        print_r($categories_model->all());
//        print_r($categories_model->get_categories());
//        print_r($categories_model->get_categories());
//        return microtime(true)-start_at;
//        $pay=new alipay();
//        $pay->pay();
//        $user->where('name','赵李杰');
//        $user->comment_list()->update();
//        print_r($user->all());
//        $goods=new goods();
//        $data=$goods->where_in('name',['分期','定金','','全款'])->delete();
//        print_r($data->all());
//        $goods->refresh();
//        foreach ($data as $item){
//            $goods->where('id',$item['id'])->update(['name'=>$item['title'],'description'=>$item['title']]);
//            $goods->refresh();
//        }
//        print_r($goods->where_like('name','¥')->get()->all());
//        ini_set('memory_limit','3072M');
//        $goods=new goods();
//        return $goods->count();
//        echo 'hello word';
//        return microtime(true)-start_at;
//        soft_db::table('admin')
//            ->integer('id',10,'not null',true,true)
//            ->string('name','50','not null')
//            ->string('tel',11)->unique()
//            ->string('login_number',50)
//            ->string('password',50)
//            ->create();
//        soft_db::table('student')
//            ->integer('id',4,'not null',true,true)
//            ->string('s_no',20)->unique()
//            ->string('name',30)
//            ->integer('age',4)
//            ->string('tel',11)->unique()
//            ->datetime('into_school')
//            ->string('college',30)
//            ->string('major',30)
//            ->string('class',30)
//            ->create();
//        queue::asyn(function (){
//            $mail=new mail();
//            $mail->send_email("1293777844@qq.com","test","jjawesome");
//        });
//        echo \system\config\config::server()["host_ip"].PHP_EOL;
//        $http=new http();
//        $http->post(\system\config\config::server()["host_ip"].":9555/123",["password"=>19971998]);
//        print_r(class_define::redis()->get("timed_task"));
//        $mail=new mail();
//        $mail->send_email("1293777844@qq.com","fsadf","j");
//        $rabbitmqp=new rabbitmq();
//        $rabbitmqp->push('test','asd',"heoolo");
//        $rabbitmqp->block_handle("test",'test','',function (){
//            echo "load";
//        });
//        while (($message=$rabbitmqp->get('test','asd'))){
//            echo $message.PHP_EOL;
//        }
//        print_r($user->where_bettween("created_at","2019-06-25 22:26:23","2019-07-09 15:14:38")->all());
//        $user->create([
//            "name"=>"jjawesome1",
//            "password"=>12255224,
//            "email"=>"2763553967@qq.com",
//            "sex"=>"man",
//        ],true);
//        print_r($user->where("created_at",">","2019-06-29 09:02:40")->count());
//        print_r($user->all());
//        return microtime(true)-start_at;
//        $catalog=new goods();
//        print_r($catalog->where('level',1)->or_where('level',2)->count(true));
//        print_r($catalog->where('level',1)->all());
//        return $catalog->where("najjme",123)->page($request->get("page",1),1000);
//        print_r(class_define::redis()->hGetAll("news_info_https://news.sina.cn/gn/2019-10-09/detail-iicezuev0947649.d.html?&cid=56261"));
//        foreach (class_define::redis()->hGetAll("news_cache_record") as $key=>$value){
//            class_define::redis()->del($key);
//        }
//        $file=new \system\file();
//        $picture_list=$file->file_walk(\system\config\config::env_path()."public/image/code_drop");
//        foreach ($picture_list as $name){
//            $time=basename($name);
//            $time=str_replace(".jpg","",$time);
//            $time=preg_replace("/_(.*?)_/","",$time);
//            if(is_numeric($time)&&strlen($time)==10){
//                if(time()-$time>3600) {
//                    $file->delete_file($name);
//                }
//            }
//        }
//        return session::all();
//        return encrypt::rsa_encrypt("test");
//        return view("monster/index");
//        return "错误了哦";
////        return microtime(true)-start_at;
//        $super=new Serializer();
//        $close=$super->serialize(function ()use ($user){
//            print_r($user->all());
//        });
//
//        call_user_func($super->unserialize($close));
        cache_::set_cache("test","123",60);
        echo cache_::get_cache("test").PHP_EOL;
        echo microtime(true)-start_at;
    }

}