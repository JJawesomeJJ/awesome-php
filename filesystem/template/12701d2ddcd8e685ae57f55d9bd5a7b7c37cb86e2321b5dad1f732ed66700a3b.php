<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" href="http://localhost//template/vivo/css/main.css">
</head>
<body>
    <div class="box">
        <div class="up" onclick="up()"></div>
        <div class="down" onclick="down()"></div>
    <div class="head">
        <div class="logo">
            <img src="http://localhost//template/vivo/img/vivo.png" alt=""/></div>

            <ul class="mo01">
                <li>iQOO专区</li>
                <li>NEX系列</li>
                <li>X系列</li>
                <li>S系列</li>
                <li>Z系列</li>
                <li>Y系列</li>
                <li>U系列</li>
                <li>商场</li>
                <li>粉丝福利</li>
            </ul>
        <div class="dl"><img src="http://localhost//template/vivo/img/shouye.png" alt=""/>
            <p>登录</p>
        </div>
        <div class="adv01">
            <h2>iQOO 性能之王</h2>
            <h1>建议零售价：￥6399</h1>
            <a style="color: lavender" href=>立即购买</a>
        </div>

        </div>
    <div class="cont1">
        <div class="adv02">
            <div class="adv02_son">
                <h2>免费定制<br/>镌刻心意</h2>
            </div>
        </div>
        <div class="adv03">
            <div class="adv03_son">
                <h2>以旧换新<br/>专业评估</h2>
            </div>
        </div>
        <div class="adv04">
           <div class="adv04_son">
               <p>>查看详情</p>
               <p>>立即购买</p>
               <p>NEX</p>
           </div>
        </div>
    </div>
    <div class="cont2">
        <div class="cont02_son son01">
            <h2>试试X27</h2>
            <p>写评测赢大奖</p>
            <div class="sell4">零售价：￥3899</div>
        </div>
        <div class="cont02_son son02">
            <h2>vivo NEX拍照很厉害？</h2>
            <p>点击立即查看</p>
            <div class="sell4">零售价：￥5399</div>
        </div>
        <div class="cont02_son son03">
            <h2>vivo自拍神器？</h2>
            <p>极致美白 神仙颜值</p>
            <div class="sell4">零售价：￥6799</div>
        </div>
        <div class="cont02_son son04">
            <h2>vivo Z3</h2>
            <p>急速AI  尽享畅快 </p>
            <div class="sell4">零售价：￥2399</div>
        </div>
        <div class="cont02_son son05">
            <h2>IQOO拍照也硬核</h2>
            <p>立即探索</p>
            <div class="sell4">零售价：￥11999</div>
        </div>
    </div>
    <div class="foot">
        <div class="headline">热门链接</div>
        <div class="headline">在线购买</div>
        <div class="headline">服务支持</div>
        <div class="headline">vivo社区</div>
        <div class="headline">关于vivo</div>
        <div class="subhead01">
        <ul class="one">
            <li>X27</li>
            <li>iQOO</li>
            <li>Z3</li>
            <li>vivo摄影</li>
            <li>查找手机</li>
            <li>常见问题</li>
        </ul>
    </div>
        <div class="subhead02">
            <ul class="one">
                <li>官方商城</li>
                <li>选购手机</li>
                <li>选购配件</li>
                <li>政企服务</li>
                <li>以旧换新</li>
                <li>服务保障</li>
            </ul>
        </div>
        <div class="subhead03">
        <ul class="one">
            <li>服务首页</li>
            <li>服务网点查询</li>
            <li>真伪查询</li>
            <li>服务政策</li>
            <li>预约维修</li>
            <li>维修配件价格</li>
        </ul>
    </div>
        <div class="subhead04">
        <ul class="one">
            <li>社区首页</li>
            <li>影视专区</li>
            <li>微博</li>
            <li>贴吧</li>
            <li>兴趣部落</li>
        </ul>
    </div>
        <div class="subhead05">
            <ul class="one">
                <li>vivo简介</li>
                <li>工作机会</li>
                <li>新闻资讯</li>
                <li>采购平台</li>
                <li>开发者平台</li>
            </ul>
        </div>
        <div class="tail">
            <div class="tail_son01">
                <div class="tail_son0101">
                </div>
                <p>电话：15681329057</p>
            </div>
            <div class="tail_son02">
                <h2>400-689-589</h2>
                <p>全国咨询电话</p>
            </div>
            <div class="tail_son03">
                <h3>关注vivo:</h3>
                <div class="dog s1"></div>
                <div class="dog s2"></div>
                <div class="dog s3"></div>
            </div>
        </div>
    </div>

    </div>
    <script>
        var max_down=0;
        function up(){
            var goup=setInterval(function(){
                if(document.documentElement.scrollTop!=0){
                    document.documentElement.scrollTop=document.documentElement.scrollTop-50

                }else{
                    clearInterval(goup)
                }
            },20)
        }
        function down(){
            var godown=setInterval(function(){
                if(max_down>=1000){
                    max_down=0;
                    clearInterval(godown)
                }
                else{
                    {
                        max_down=max_down+50;
                        document.documentElement.scrollTop=document.documentElement.scrollTop+50}
                }
            },20)
        }
//        function down(){
//            var godown=setInterval(function(){
//                if(document.documentElement.scrollTop>=1070&&document.documentElement.scrollTop>=1140){
//                    clearInterval(godown)
//                }
//                else{
//                    {
//                        document.documentElement.scrollTop=document.documentElement.scrollTop+50}
//                }
//            },20)
//        }
      /*  function judge_down(){
            if(document.documentElement.scrollTop<2344)
            {
                down();
            }
        }*/
    </script>
</body>
</html>