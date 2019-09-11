<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="http://www.titang.shop/template/component/header/css/header.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.staticfile.org/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="http://www.titang.shop/template/component/header/../../asset/bootstrap4/bootstrap4.min.css">
    <link href="http://www.titang.shop/template/component/header/../../asset/font_3giswyavnyx/iconfont1.css" rel="stylesheet">
    <script src="http://www.titang.shop/template/component/header/../../asset/bootstrap4/1.12.5popper.min.js"></script>
    <script src="http://www.titang.shop/template/component/header/../../asset/bootstrap4/bootstrap4.10.min.js"></script>
    <link href="http://www.titang.shop/template/component/header/../../asset/css/common.css" rel="stylesheet">
    <script src="http://www.titang.shop/template/component/header/../../asset/js/dom_op.js"></script>
</head>
<body>
<div class="header no-gutters" style="z-index: 1000;overflow: hidden;">
<nav class="navbar navbar-expand-md bg-primary">
    <button class="navbar-toggler mr-auto bg-muted j-toggle" type="button" data-toggle="collapse" data-target="#user_info">
        <span class="iconfont icon-icon-test39 text-white"></span>
    </button>
    <div class="title ml-md-auto offset-2">
        <h2 class="text-white">TITANG控制台</h2>
    </div>
    <div class="collapse navbar-collapse ml-auto col-sm-2 col-lg-3 col-4" id="collapsibleNavbar">
        <ul class="navbar-nav ml-auto col no-gutters">
            <li class="nav-item d-flex flex-row col-lg-3 j-toggle col-sm-8 col-12 ml-auto" data-toggle="collapse" data-target="#user_info">
                <div class="head_img ml-auto" style="width: 50px;height: 50px;">
                    <div style="height: 100%;width: 100%">
                        <img class="img-circle rounded-circle img-fluid" :src=user_info.head_img alt="">
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div class="col-lg-3 col-sm-4 col-md-8 bg-light show_width collapse col-md-4 ml-auto rounded " style="position: fixed;right: 0px;z-index:1000" id="user_info">
    <div class="container fill_container no-gutters m-0">
        <div class="row">
            <div class="head_img j-center mt-1"><img :src=user_info.head_img  alt=""></div>
            <div class="name container-fluid"><h5 class="text-center text-primary" style="width: 100%;!important;">{{name}}</h5></div>
        </div>
        <div class="row no-gutters fill_container mt-1">
            <div class="user_permission fill_container d-flex flex-row container-fluid">
                <span class="iconfont icon-icon-test35 text-white rounded-circle icon-info bg-warning text-center"></span>
                <h6 class="text-right text-success mr-auto" style="width: 100%;">{{permission}}</h6>
            </div>
        </div>
        <div class="row no-gutters mt-1">
            <div class="user_permission fill_container d-flex flex-row container-fluid">
                <div class="iconfont ml-auto icon-icon-test33 text-center rounded-circle text-white icon-info no-gutters bg-primary"></div>
                <h6 class="text-right text-success" style="width: 100%;">{{user_info.email}}</h6>
            </div>
        </div>
        <div class="row no-gutters mt-1 p-0" style="height: 30px;">
            <div class="user_permission fill_container d-flex flex-row container-fluid">
                <span class="iconfont icon-icon-test5 text-center bg-dark text-white rounded-circle icon-info"></span>
                <h6 class="text-right text-success" style="width: 100%;">1997-3-20</h6>
            </div>
        </div>
        <div class="row align-bottom mt-1 ml-md-auto">
            <div class="user_permission d-flex flex-row">
                <span class="fa fa-sign-out"></span>
            </div>
        </div>
        <div class="row bg-danger rounded no-gutters p-0 j-center" style="height: 40px;">
            <div class="name container-fluid"><h5 class="text-center text-white align-content-center" style="width: 100%; line-height:40px;">退出</h5></div>
        </div>
    </div>
</div>
</div>
</body>
</html>