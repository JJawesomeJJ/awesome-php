<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7 0007
 * Time: 上午 8:50
 */

namespace task\job;


use system\file;
use system\mail;
use task\queue\queue_handle;
use template\compile;
require_once dirname(dirname(__DIR__))."/load/auto_load.php";
class email_queue extends queue_handle
{
    protected $listen_queue="email";
    public function handle(array $queue)
    {
        $mail=new mail();
        $compile=new compile();
        $file=new file();
        $img=$file->img_base64(dirname(dirname(dirname(__DIR__)))."/image/logo.png");
        $queue["data"]["img"]=$img;
        $content=$compile->view($queue["data"]["template"],$queue["data"]);
        $mail->send_email($queue["data"]["user"],$content,$queue["data"]["title"]);
    }
}
new email_queue();