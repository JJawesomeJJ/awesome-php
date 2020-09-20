<?php
/**
 * Created by awesome.
 * Date: 2019-04-10 10:34:30
 */
namespace app\controller\index;
use app\controller\auth\auth_controller;
use app\Event\native_push_event;
use app\Event\user_login_event;
use app\controller\code\code_controller;
use app\controller\controller;
use db\db;
use db\factory\migration\migration;
use db\factory\migration\migration_list\migration_comment_list;
use db\factory\soft_db;
use db\factory\SqlRouter;
use db\model\comment_list\comment_list;
use db\model\model;
use db\model\model_auto\model_auto;
use db\model\park\map;
use db\model\shop\categories;
use db\model\shop\goods;
use db\model\user\user;
use extend\awesome\awesome_echo_tool;
use Grafika\Grafika;
use JJMysql\MysqlClient;
use JJMysqlClient\api\driver\mysql\MysqlConnect;
use JJMysqlClient\connection;
use load\auto_load;
use load\provider;
use load\provider_register;
use request\request;
use SebastianBergmann\CodeCoverage\Report\PHP;
use system\cache\abstract_\driver\CacheDriverFile;
use system\cache\abstract_\driver\CacheDriverRedis;
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
use system\lock;
use system\LuaScript;
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
    public function index(request $request)
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
//        $picture_list=$file->okkokmmllklom,(\system\config\config::env_path()."public/image/code_drop");
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
//        cache_::set_cache("test","123",60);
//        echo cache_::get_cache("test").PHP_EOL;
//        $rabitmq=new rabbitmq();
//        echo microtime(true)-start_at;
//        $cache=new cache();
////        event("user_login_event");
//        if(($num=$cache->lock_get_cache('num'))==null){
//            $num=1;
//            $cache->set_cache('num',1,120000);
//        }
//        $cache->lock_set_cache('num',$num+1,120000);
//        $rabbitmq=new rabbitmq();
//        return $rabbitmq->push('timer2','timer2',$num,'',10);
//////        $mail=new mail();
//        $mail->send_email('1293777844@qq.com','ds','ds');
//        lock::redis_lock('language',5,10);
//        return 1;
//        $data=[];
//        fd
//        return $data['dfd'];
//        return $this->download("D:\\123\\123.txt",'jjawesome.app');
//        queue::asyn(function (){
//            $mail=new mail();
//            $mail->send_email('1293777844@qq.com','test','test');
//        });
//        event("test");
//        $editor = Grafika::createEditor();
//        app()->bind(\Redis::class,function (){
//            return class_define::redis();
//        });
//        $redis=make(\Redis::class);
//        $redis->get("test");
//        $user=new user();
//        $user->find(1);
//        event(new user_login_event((new user())->all()));
//        return microtime(true)-start_at;
//        return $this->sort([1,5,9,10,2,5,7,6,1,4,10]);
//        class_define::redis()->del('test');
//        LuaScript::hash_add_array('test','test','sdf');
//        var_dump(LuaScript::hash_del_key('tet1','dfd','fdfdf'));
//        return json_decode(class_define::redis()->hGet('tet1','dfd'),true);
//        var_dump(class_define::redis()->hGetAll('test'));
//        $rabbitmq=new rabbitmq();
//        return runtime();
//        return $awesome_echo_tool->get_online_users();
//        class_define::redis()->del('test123');
//        LuaScript::hash_hash_add_key_value('test123','test123','test4','tes2t123',time());
//        return class_define::redis()->hGet('test123','test123');
//        return $awesome_echo_tool->get_online_users();
//        LuaScript::hash_add_array("AAA","AAA","AAA");
//        return LuaScript::hash_arr_len("AAA","AAA");
//        $user=new user();
//        $user->transactions(function () use ($user){
//            echo $user->where("name","jjawesome")->get()->name;
//            $user->update(['name'=>"李春梅"]);
//            throw new \Exception("TEST");
//        });
//        class_define::redis()->del("sql-router");
//        auto_load::load_extend("mysql");
//        $config=[
//            "user"=>"root",
//            "password"=>".zlj19971998",
//            'database'=>"register",
//            'ip'=>'127.0.0.1',
//            'port'=>3306
//        ];
//        $user = $config["user"];
//        $password =$config["password"];
//        $database =$config["database"];
//        $host = $config['ip'];
//        $port=$config['port'];
//        $dsn = "mysql".":host=$host;port=$port;dbname=$database;charset=utf8";
//        $arr=[];
//        $time=microtime(true);
//        $pdo=(new \PDO($dsn,$user,$password));
//        for($i=0;$i<1000;$i++) {
//
//            print_r($pdo->query("select * from user")->fetchAll());
////            $arr[] = new connection("127.0.0.1", '3399');
//            echo $i.PHP_EOL;
//        }
////        echo (microtime(true)-$time).PHP_EOL;
////        for($i=0;$i<1;$i++) {
////            new MysqlConnect("127.0.0.1", '3399',$config['database'],$config['user'],$password);
////        }
//        $arr=[];
////        sleep(5);
////        print_r($arr);
//        return runtime();
//        $MysqlClient->send("FSDFS");
//        $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
//        return $_SERVER['SERVER_ADDR'];
//        return $user->where('id',1)->update([
//            "sex"=>"women"
//        ]);
//        echo  LuaScript::hash_decrease("tes2551t","jjawegfso33me");
//        $cache=new CacheDriverRedis();
////        $cache->del("test");;
////        $file=new file();
////        print_r($file->file_walk(config::env_path()));
////        var_dump($cache->decrBy("test",1,1,6));
//        $cache=new CacheDriverFile();
////        $cache->del("jjawesome");
//        $cache->incrBy("jjawesome");
//        return $cache->get("jjawesome");
//        print_r($user->ReadMaster()->all());
//        return $user->ReadMaster()->where("name","赵李杰")->update([
//            "sex"=>"man"
//        ]);
        $num=<<<num
18
20
12
18
13
15
15
17
14
12
12
22
14
13
20
14
13
14
11
12
19
12
11
10
11
10
29
16
21
16
11
21
19
11
16
22
17
18
12
12
27
20
14
20
13
13
19
13
22
14
17
13
10
15
12
12
10
24
15
12
17
16
29
17
10
15
25
30
17
28
10
31
27
29
17
13
17
27
21
17
21
11
12
16
20
10
17
21
16
11
13
13
14
15
10
20
13
15
17
12
21
26
16
18
17
21
15
17
17
17
27
28
19
15
18
19
18
19
28
20
15
24
24
26
28
13
30
18
11
19
17
18
10
20
14
11
20
12
13
12
17
23
17
15
10
15
31
20
18
12
18
12
18
14
18
15
18
14
15
12
12
14
14
13
12
13
10
19
12
16
11
10
17
13
13
13
16
14
19
11
25
27
21
10
14
21
24
21
17
10
13
13
17
11
16
10
18
10
11
17
13
20
15
10
11
24
10
15
18
17
15
20
22
13
20
10
23
13
20
15
17
14
13
33
28
16
16
21
12
18
18
12
14
10
23
14
14
23
20
11
21
20
10
13
15
20
18
14
18
14
25
15
14
15
41
20
14
14
19
12
13
10
17
20
20
28
14
12
20
11
16
21
13
14
15
12
13
12
12
19
12
13
12
11
21
16
14
18
31
10
17
12
11
11
12
10
13
14
15
17
18
18
10
14
17
21
18
19
14
24
10
17
21
21
15
14
29
17
15
13
23
30
11
13
18
10
14
17
15
15
20
13
33
18
13
19
15
14
10
12
12
10
10
22
12
10
16
19
11
15
17
10
19
11
13
21
15
25
25
15
12
15
15

num;
        $num=explode(PHP_EOL,$num);
        return array_sum($num);
        return DataMap::GetMap();
        $data=<<<data
中共苏州市吴江区江陵街道湖东社区支部委员会
中共苏州市吴江区江陵街道西湖社区委员会
中共苏州市吴江区江陵街道西湖社区第一支部委员会
中共苏州市吴江区江陵街道西湖社区第二支部委员会
中共苏州市吴江区江陵街道花港村支部委员会
中共苏州市吴江区江陵街道新柳溪社区委员会
中共苏州市吴江区江陵街道新柳溪社区新柳支部委员会
中共苏州市吴江区江陵街道九龙村支部委员会
中共苏州市吴江区江陵街道江陵社区委员会
中共苏州市吴江区江陵街道江陵社区西塘支部委员会
中共苏州市吴江区江陵街道江陵社区吴越支部委员会
中共苏州市吴江区江陵街道淞南社区支部委员会
中共苏州市吴江区江陵街道三里桥社区委员会
中共苏州市吴江区江陵街道三兴村支部委员会
中共苏州市吴江区江陵街道三里桥社区三里桥支部委员会
中共苏州市吴江区江陵街道三里桥社区庞北支部委员会
中共苏州市吴江区江陵街道庞山湖社区总支部委员会
中共苏州市吴江区江陵街道庞山湖社区云梨支部委员会
中共苏州市吴江区江陵街道庞山湖社区庞南支部委员会
中共苏州市吴江区江陵街道山湖社区委员会
中共苏州市吴江区江陵街道山湖社区第一支部委员会
中共苏州市吴江区江陵街道山湖社区第二支部委员会
中共苏州市吴江区江陵街道山湖社区第三支部委员会
中共苏州市吴江区江陵街道叶津村支部委员会
中共苏州市吴江区江陵街道运东社区支部委员会
中共苏州市吴江区江陵街道雅辉社区总支部委员会
中共苏州市吴江区江陵街道雅辉社区弘雅苑支部委员会
中共苏州市吴江区江陵街道雅辉社区鸿辉苑支部委员会
中共苏州市吴江区江陵街道联兴村委员会
中共苏州市吴江区江陵街道联兴村同兴支部委员会
中共苏州市吴江区江陵街道联兴村厍浜支部委员会
中共苏州市吴江区江陵街道联兴村仪塔支部委员会
中共苏州市吴江区江陵街道叶泽湖花苑社区委员会
中共苏州市吴江区江陵街道叶泽湖村支部委员会
中共苏州市吴江区江陵街道叶泽湖花苑社区东港支部委员会
中共苏州市吴江区江陵街道叶泽湖花苑社区西港支部委员会
中共苏州市吴江区江陵街道新港社区委员会
中共苏州市吴江区江陵街道龙杨村支部委员会
中共苏州市吴江区江陵街道新港社区第一支部委员会
中共苏州市吴江区江陵街道新港社区第二支部委员会
中共苏州市吴江区江陵街道城南社区委员会
中共苏州市吴江区江陵街道三庞村支部委员会
中共苏州市吴江区江陵街道城南社区第一支部委员会
中共苏州市吴江区江陵街道城南社区第二支部委员会
中共苏州市吴江区江陵街道城南社区第三支部委员会
中共苏州市吴江区江陵街道益联村总支部委员会
中共苏州市吴江区江陵街道益联村西联支部委员会
中共苏州市吴江区江陵街道益联村凌益支部委员会
中共吴江经济技术开发区天和小学支部委员会
中共吴江经济技术开发区实验初级中学支部委员会
中共吴江经济技术开发区长安实验小学支部委员会
中共吴江经济技术开发区山湖花园小学支部委员会
中共吴江经济技术开发区花港迎春小学支部委员会
中共苏州市吴江区江陵街道机关江城支部委员会
中共苏州市吴江区江陵街道机关新城支部委员会
中共苏州市吴江区江陵街道综合执法局支部委员会
中共吴江经济技术开发区江陵实验初级中学支部委员会
data;
        $code=<<<code
001.001.032.005.005.010.002
001.001.032.005.005.010.002.801
001.001.032.005.005.010.002.802
001.001.032.005.005.010.002.802.001
001.001.032.005.005.010.002.802.002
001.001.032.005.005.010.002.802.003
001.001.032.005.005.010.002.803
001.001.032.005.005.010.002.803.001
001.001.032.005.005.010.002.803.002
001.001.032.005.005.010.002.804
001.001.032.005.005.010.002.804.001
001.001.032.005.005.010.002.804.002
001.001.032.005.005.010.002.805
001.001.032.005.005.010.002.806
001.001.032.005.005.010.002.806.001
001.001.032.005.005.010.002.806.002
001.001.032.005.005.010.002.806.003
001.001.032.005.005.010.002.807
001.001.032.005.005.010.002.807.001
001.001.032.005.005.010.002.807.002
001.001.032.005.005.010.002.808
001.001.032.005.005.010.002.808.001
001.001.032.005.005.010.002.808.002
001.001.032.005.005.010.002.808.003
001.001.032.005.005.010.002.808.004
001.001.032.005.005.010.002.809
001.001.032.005.005.010.002.810
001.001.032.005.005.010.002.810.001
001.001.032.005.005.010.002.810.002
001.001.032.005.005.010.002.811
001.001.032.005.005.010.002.811.001
001.001.032.005.005.010.002.811.002
001.001.032.005.005.010.002.811.003
001.001.032.005.005.010.002.812
001.001.032.005.005.010.002.812.001
001.001.032.005.005.010.002.812.002
001.001.032.005.005.010.002.812.003
001.001.032.005.005.010.002.813
001.001.032.005.005.010.002.813.001
001.001.032.005.005.010.002.813.002
001.001.032.005.005.010.002.813.003
001.001.032.005.005.010.002.814
001.001.032.005.005.010.002.814.001
001.001.032.005.005.010.002.814.002
001.001.032.005.005.010.002.814.003
001.001.032.005.005.010.002.814.004
001.001.032.005.005.010.002.815
001.001.032.005.005.010.002.815.001
001.001.032.005.005.010.002.815.002
001.001.032.005.005.010.002.890
001.001.032.005.005.010.002.891
001.001.032.005.005.010.002.892
001.001.032.005.005.010.002.893
001.001.032.005.005.010.002.894
001.001.032.005.005.010.002.895
001.001.032.005.005.010.002.896
001.001.032.005.005.010.002.897
001.001.032.005.005.010.002.898

code;
       $code=<<<code
001.001.032.005.005.010.004
001.001.032.005.005.010.004.111
001.001.032.005.005.010.004.111.001
001.001.032.005.005.010.004.111.002
001.001.032.005.005.010.004.111.004
001.001.032.005.005.010.004.112
001.001.032.005.005.010.004.112.001
001.001.032.005.005.010.004.112.002
001.001.032.005.005.010.004.112.003
001.001.032.005.005.010.004.112.004
001.001.032.005.005.010.004.555
001.001.032.005.005.010.004.555.001
001.001.032.005.005.010.004.877
001.001.032.005.005.010.004.888
001.001.032.005.005.010.004.889
001.001.032.005.005.010.004.890
001.001.032.005.005.010.004.891
001.001.032.005.005.010.004.892
001.001.032.005.005.010.004.893
001.001.032.005.005.010.004.894
001.001.032.005.005.010.004.895
001.001.032.005.005.010.004.896
001.001.032.005.005.010.004.897
001.001.032.005.005.010.004.898
001.001.032.005.005.010.004.899
001.001.032.005.005.010.004.900
001.001.032.005.005.010.004.901
001.001.032.005.005.010.004.902
001.001.032.005.005.010.004.903
001.001.032.005.005.010.004.904
001.001.032.005.005.010.004.905
001.001.032.005.005.010.004.905.001
001.001.032.005.005.010.004.905.002
001.001.032.005.005.010.004.905.004
001.001.032.005.005.010.004.905.005
001.001.032.005.005.010.004.906
001.001.032.005.005.010.004.906.001
001.001.032.005.005.010.004.906.002
001.001.032.005.005.010.004.906.003
001.001.032.005.005.010.004.907
001.001.032.005.005.010.004.907.001
001.001.032.005.005.010.004.907.003
001.001.032.005.005.010.004.908
001.001.032.005.005.010.004.908.001
001.001.032.005.005.010.004.908.002
001.001.032.005.005.010.004.908.003
001.001.032.005.005.010.004.909
001.001.032.005.005.010.004.909.001
001.001.032.005.005.010.004.909.002
001.001.032.005.005.010.004.909.003
001.001.032.005.005.010.004.909.006
001.001.032.005.005.010.004.909.007
001.001.032.005.005.010.004.909.008
001.001.032.005.005.010.004.909.009
001.001.032.005.005.010.004.910
001.001.032.005.005.010.004.910.001
001.001.032.005.005.010.004.910.002
001.001.032.005.005.010.004.910.003
001.001.032.005.005.010.004.910.005
001.001.032.005.005.010.004.911
001.001.032.005.005.010.004.911.001
001.001.032.005.005.010.004.911.002
001.001.032.005.005.010.004.911.003
001.001.032.005.005.010.004.912
001.001.032.005.005.010.004.912.001
001.001.032.005.005.010.004.912.002
001.001.032.005.005.010.004.912.003
001.001.032.005.005.010.004.913
001.001.032.005.005.010.004.913.001
001.001.032.005.005.010.004.913.002
001.001.032.005.005.010.004.913.003
001.001.032.005.005.010.004.913.004
001.001.032.005.005.010.004.914
001.001.032.005.005.010.004.915
001.001.032.005.005.010.004.915.001
001.001.032.005.005.010.004.915.002
001.001.032.005.005.010.004.916
001.001.032.005.005.010.004.917
001.001.032.005.005.010.004.917.001
001.001.032.005.005.010.004.917.002
001.001.032.005.005.010.004.917.003
001.001.032.005.005.010.004.918
001.001.032.005.005.010.004.918.001
001.001.032.005.005.010.004.918.002
001.001.032.005.005.010.004.918.003
001.001.032.005.005.010.004.919
001.001.032.005.005.010.004.920
001.001.032.005.005.010.004.921
001.001.032.005.005.010.004.922
001.001.032.005.005.010.004.923
code;
        return soft_db::table("TP_ZZB_DYXX_NEW")
            ->where_in("ORGCODE",explode(PHP_EOL,$code))
            ->count("*")->get();
//        $name=explode(PHP_EOL,$data);
//        $code=explode(PHP_EOL,$code);
//        $online=soft_db::table("t_partyuser_back")
//            ->setConnect("47.97.11.193","8066","dsj_wjjkq_jljd_show","mycat","NwJdyBd78g5yTvSq5OJ22D0M82r4oFbD");
//        $data=soft_db::table("TP_ZZB_DYXX_NEW")->count("1")->where_in("ORGCODE",$code)->all()->get();
////    /
//        $insert_=[];
//        set_time_limit(0);
//        foreach ($data as $datum){
//            $insert_[]=[
//                "name" => $datum["NAME"],
//                "sex" => $datum["SEX"],
//                "born" => $datum["BIRTHDAY"],
//                "belong" => $datum["NATIVEPLACE"],
//                "education" => $datum["EDUCTION"],
//                "into_party" => $datum["JOINDATE"],
//                "party_name" => $datum["ORGNAME"],
//                "party_type" => $datum["ORGDOMAINTYPE"],
//                "party_id" => $datum["ORGCODE"],
//                "area" => $datum["SSZMC"],
//                "id" => $datum["PKID"],
//                "created_at" => "2020-08-16 00:28:27"
//            ];
//            try {
//                $online->insert($insert_);
//                $online->refresh();
//            }catch (\Exception $exception){
////                echo $exception->getMessage();
////                die();
//            }
//        }
//        return $online=soft_db::table("t_partyuser_back")
//            ->setConnect("47.97.11.193","8066","dsj_wjjkq_jljd_show","mycat","NwJdyBd78g5yTvSq5OJ22D0M82r4oFbD")
//            ->count("id")->get();

    }

    /**
     * @return false|\PDOStatement
     */
    public function query(){
        $config=[
            "user"=>"root",
            "password"=>".zlj19971998",
            'database'=>"register",
            'ip'=>'127.0.0.1',
            'port'=>3306
        ];
        $user = $config["user"];
        $password =$config["password"];
        $database =$config["database"];
        $host = $config['ip'];
        $port=$config['port'];
        $dsn = "mysql".":host=$host;port=$port;dbname=$database;charset=utf8";
        return (new \PDO($dsn,$user,$password))->query("select * from user");
    }
    public function quick(array $arr){
        if(count($arr)<=1){
            return $arr;
        }
        $base=$arr[0];
        $left=[];
        $right=[];
        for($i=1;$i<count($arr);$i++){
            if($base>=$arr[$i]){
                $left[]=$arr[$i];
            }else{
                $right[]=$arr[$i];
            }
        }
        $left=$this->quick($left);
        $right=$this->quick($right);
        return array_merge($left,[$base],$right);
    }
    public function sort(array $arr){
        for($i=0;$i<count($arr);$i++){
            for($j=$i+1;$j<count($arr);$j++){
                if($arr[$j]>$arr[$i]){
                    $swap=$arr[$j];
                    $arr[$j]=$arr[$i];
                    $arr[$i]=$swap;
                }
            }
        }
        return $arr;
    }
}