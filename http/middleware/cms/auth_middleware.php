<?php

namespace http\middleware\cms;

use app\controller\admin_user\admin_user_controller;
use http\middleware\middleware;
use system\session;

class auth_middleware extends middleware
{
    public function check()
    {
        if(!session::exist("admin")){
            admin_user_controller::permission(false);
            if(!session::exist("admin")){
                redirect("/admin/user");
            }
        }
    }
}