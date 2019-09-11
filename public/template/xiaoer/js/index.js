$(document).ready(function(){

    var data = {
        timea:null,
        bannertxt:"",		//banner 鎻愪氦琛ㄥ崟
        bannername:"",
        bannerphone:"",
        headipt:"",	//椤堕儴
        popadd:[],
        popdata:"",
        popname: "", 	//寮圭獥ipt
        animate: false,
        indextop: true, //澶撮儴骞垮憡鍏抽棴

        //杩欓噷鏄儹闂ㄥ晢鏍囩殑鏁版嵁
        num: 0, //榛樿绉诲叆鏁堟灉
        num1: 0,
        num2: 0,
        num3: 0,
        num4: 0,
        num5: 0,
        imgs1: ["/kuxiaoer/images/theHead/icon1-1.jpg", "/kuxiaoer/images/theHead/icon1-2.jpg",
            "/kuxiaoer/images/theHead/icon1-3.jpg", "/kuxiaoer/images/theHead/icon1-4.jpg",
            "/kuxiaoer/images/theHead/icon1-5.jpg", "/kuxiaoer/images/theHead/icon1-5.jpg",
            "/kuxiaoer/images/theHead/icon1-4.jpg", "/kuxiaoer/images/theHead/icon1-3.jpg",
            "/kuxiaoer/images/theHead/icon1-2.jpg", "/kuxiaoer/images/theHead/icon1-1.jpg"
        ],
        imgs2: ["/kuxiaoer/images/theHead/icon1-2.jpg", "/kuxiaoer/images/theHead/icon1-3.jpg",
            "/kuxiaoer/images/theHead/icon1-4.jpg", "/kuxiaoer/images/theHead/icon1-5.jpg",
            "/kuxiaoer/images/theHead/icon1-1.jpg", "/kuxiaoer/images/theHead/icon1-5.jpg",
            "/kuxiaoer/images/theHead/icon1-4.jpg", "/kuxiaoer/images/theHead/icon1-3.jpg",
            "/kuxiaoer/images/theHead/icon1-2.jpg", "/kuxiaoer/images/theHead/icon1-1.jpg"
        ],
        imgs3: ["/kuxiaoer/images/theHead/icon1-3.jpg", "/kuxiaoer/images/theHead/icon1-4.jpg",
            "/kuxiaoer/images/theHead/icon1-5.jpg", "/kuxiaoer/images/theHead/icon1-1.jpg",
            "/kuxiaoer/images/theHead/icon1-2.jpg", "/kuxiaoer/images/theHead/icon1-5.jpg",
            "/kuxiaoer/images/theHead/icon1-4.jpg", "/kuxiaoer/images/theHead/icon1-3.jpg",
            "/kuxiaoer/images/theHead/icon1-2.jpg", "/kuxiaoer/images/theHead/icon1-1.jpg"
        ],
        imgs4: ["/kuxiaoer/images/theHead/icon1-4.jpg", "/kuxiaoer/images/theHead/icon1-5.jpg",
            "/kuxiaoer/images/theHead/icon1-1.jpg", "/kuxiaoer/images/theHead/icon1-2.jpg",
            "/kuxiaoer/images/theHead/icon1-3.jpg", "/kuxiaoer/images/theHead/icon1-5.jpg",
            "/kuxiaoer/images/theHead/icon1-4.jpg", "/kuxiaoer/images/theHead/icon1-3.jpg",
            "/kuxiaoer/images/theHead/icon1-2.jpg", "/kuxiaoer/images/theHead/icon1-1.jpg"
        ],
        tmk1: ["鏅€氶噾灞炲強鍏跺悎閲�", "閲戝睘寤虹瓚鏉愭枡", "鏃ョ敤浜旈噾鍣ㄥ叿", "閲戝睘瀹瑰櫒"],
        tmk2: ["鍘ㄦ埧鐐婁簨鐢ㄥ叿鍙婂鍣�", "鐡峰櫒锛岄櫠鍣�", "鐜荤拑銆佺摲銆侀櫠鐨勫伐鑹哄搧", "鑼跺叿銆侀厭鍏枫€佸挅鍟�"],
        tmk3: ["琛ｇ墿", "闉�", "甯�", "琚�"],
        tmk4: ["鍜栧暋锛屽挅鍟′唬鐢ㄥ搧锛屽彲鍙�", "鑼躲€佽尪楗枡", "绯栨灉锛屽崡濉橈紝绯�", "闈㈠寘锛岀硶鐐�"],
        tmk5: ["鍟ら厭", "涓嶅惈閰掔簿楗枡", "绯栨祮鍙婂叾浠栦緵楗枡鐢ㄧ殑鍒跺墏"],
        tmk6: ["璐甸噸閲戝睘鍙婂叾鍚堥噾", "璐甸噸閲戝睘鐩�", "鐝犲疂锛岄楗帮紝瀹濈煶鍙婅吹閲嶉噾灞炲埗绾康鍝�", "閽燂紝琛紝璁℃椂鍣ㄥ強鍏堕浂閮ㄤ欢"],
        roll: [{phohe: "133****5454",class: "25绫�",txt: "璐拱鎴愬姛"},
            {phohe: "135****7420",class: "10绫�",txt: "璐拱鎴愬姛"},
            {phohe: "181****3954",class: "33绫�",txt: "璐拱鎴愬姛"},
            {phohe: "133****3484",class: "05绫�",txt: "璐拱鎴愬姛"},
            {phohe: "136****3204",class: "01绫�",txt: "璐拱鎴愬姛"}
        ],
        roll2: [{phohe: "135****3959",class: "07绫�",txt: "璐拱鎴愬姛"},
            {phohe: "186****8238",class: "12绫�",txt: "璐拱鎴愬姛"},
            {phohe: "137****7637",class: "22绫�",txt: "璐拱鎴愬姛"},
            {phohe: "182****2636",class: "28绫�",txt: "璐拱鎴愬姛"},
            {phohe: "189****5356",class: "36绫�",txt: "璐拱鎴愬姛"}
        ],
        second: null,
        day: null,
        hour: null,
        minute: null,

    };

    var vm = new Vue({
        el: '#app',
        data: data,
        created() {
            setInterval(this.scroll, 2000);
            setInterval(this.scroll2, 2000);
        },
        methods: {
            //鏂囧瓧婊氬姩鏁堟灉
            //璁＄畻鍊掕鏃�
            count_time(timestamp) {
                var now = Date.parse(new Date()) / 1000;
                var after = timestamp;
                return {
                    day: parseInt((after - now) / (60 * 60 * 24)),
                    hour: parseInt(((after - now) % (60 * 60 * 24)) / (60 * 60)),
                    minute: parseInt((after - now) % (60 * 60) / 60),
                    second: parseInt(((after - now) % 60) / 1)
                }
            },
            count_down(timestamp) {
                var that = this;
                if (this.day == null) {
                    setInterval(function() {
                        var time = that.count_time(timestamp);
                        if(time.day==0&&time.hour==0&&time.minute==0&&time.second==0){
                            window.location.reload();//鍊掕鏃剁粨鏉熸洿鏂版暟鎹埛鏂伴〉闈�
                        }
                        for (var i in time){
                            if(time[i]<10){
                                time[i]='0'+time[i];
                            }
                        }
                        that.day = time.day;
                        that.hour = time.hour;
                        that.minute = time.minute;
                        that.second = time.second;
                    }, 1000);
                }
                return true;
            },
            scroll() {
                var that = this;
                that.animate = true; // 鍥犱负鍦ㄦ秷鎭悜涓婃粴鍔ㄧ殑鏃跺€欓渶瑕佹坊鍔燾ss3杩囨浮鍔ㄧ敾锛屾墍浠ヨ繖閲岄渶瑕佽缃畉rue
                setTimeout(() => {
                    that.roll.push(that.roll[0]); // 灏嗘暟缁勭殑绗竴涓厓绱犳坊鍔犲埌鏁扮粍鐨�
                    that.roll.shift(); //鍒犻櫎鏁扮粍鐨勭涓€涓厓绱�
                    that.animate = false; // margin-top 涓�0 鐨勬椂鍊欏彇娑堣繃娓″姩鐢伙紝瀹炵幇鏃犵紳婊氬姩
                }, 500);
            },
            //澶撮儴鍏抽棴
            topimg: function() {
                this.indextop = false;
            },
            scroll2() {
                var that = this;
                that.animate = true; // 鍥犱负鍦ㄦ秷鎭悜涓婃粴鍔ㄧ殑鏃跺€欓渶瑕佹坊鍔燾ss3杩囨浮鍔ㄧ敾锛屾墍浠ヨ繖閲岄渶瑕佽缃畉rue
                setTimeout(() => {
                    that.roll2.push(that.roll2[0]); // 灏嗘暟缁勭殑绗竴涓厓绱犳坊鍔犲埌鏁扮粍鐨�
                    that.roll2.shift(); //鍒犻櫎鏁扮粍鐨勭涓€涓厓绱�
                    that.animate = false; // margin-top 涓�0 鐨勬椂鍊欏彇娑堣繃娓″姩鐢伙紝瀹炵幇鏃犵紳婊氬姩
                }, 500);
            },

            //澶撮儴鍏抽棴
            topimg: function() {
                this.indextop = false;
            },
            //banner 鎻愪氦
            bannergb:function(){
                $("#banner-tc").css("display","none");
            },
            banneript:function(){
                if(this.bannername==""){
                    this.bannertxt = "鍟嗘爣鍚嶄笉鑳戒负绌�"
                    $("#banner-tc").css("display","block");
                    if(!(this.timea==null)){
                        clearInterval(this.timea);
                        this.timea=null;
                    }
                    this.timea = setInterval(this.bannergb, 2500);
                }else if(this.bannerphone == ""){
                    this.bannertxt = "鎵嬫満鍙蜂笉鑳戒负绌�"
                    $("#banner-tc").css("display","block");
                    if(!(this.timea==null)){
                        clearInterval(this.timea);
                        this.timea=null;
                    }
                    this.timea = setInterval(this.bannergb, 2500);
                }else if(!isMobileNumber2(this.bannerphone)){
                    this.bannertxt = "鎵嬫満鍙蜂笉鍚堟硶"
                    $("#banner-tc").css("display","block");
                    if(!(this.timea==null)){
                        clearInterval(this.timea);
                        this.timea=null;
                    }
                    this.timea = setInterval(this.bannergb, 2500);
                }else{
                    axios.post('/brand/ajaxaskbuy',Qs.stringify({
                        beizu: this.bannername,
                        tel: this.bannerphone
                    }))
                        .then((res)=>{
                            console.log(res)
                            this.bannername ="",
                                this.bannerphone = ""
                            this.bannertxt = "鎻愪氦鎴愬姛"
                            $("#banner-tc").css("display","block");
                            if(!(this.timea==null)){
                                clearInterval(this.timea);
                                this.timea=null;
                            }
                            this.timea = setInterval(this.bannergb, 2500);
                        })
                }

            },



            //寮圭獥鍏抽棴
            popx: function(){
                $(".popup").css("display","block");
                $(".popup-warp").css("display","block");
            },
            popxx: function(){
                $(".popup").css("display","none");
                $(".popup-warp").css("display","none");
            },
            //寮圭獥鎼滅储
            popss: function(){
                if(this.popname == ""){
                    return;
                }else{
                    axios.post('/brand/class',Qs.stringify({
                        business_name: this.popname
                    })).then((res)=>{
                        if(res.data.resault==null){
                            this.popadd = "";
                            this.popdata = 0;
                        }else{
                            console.log(res.data.resault.data)
                            this.popadd = res.data.resault.data;
                            this.popdata = res.data.resault.totalItems;
                        }


                    })
                }
            },
            //璺宠浆
            popa:function(){
                return window.location.href = '/brand/class?business_name='+this.popname;
            },
            popaa:function(a,b,c){
                return window.location.href = '/brand/buy?numbers='+a+'&groups='+b+'&goods='+c;
            },
            heada:function(){
                if(this.headipt == ""){
                    return
                }else{
                    return window.location.href = '/brand/buy?goods='+this.headipt;
                }
            },

            //window寮圭獥
            windowtc:function(){
                console.log("寮圭獥")
                $(".window-warp").css("display","block");
            }



        },
        mounted() {
            //杩斿洖椤堕儴
            $("#fh_top").on("click",function(){
                $('html,body').animate({scrollTop:0},500);
            })

            $(".prm").mouseenter(function(){			//绉诲叆鏁堟灉
                var bro=$(this).parent().children();
                bro.each(function () {
                    $(this).removeClass("actives");
                })
                $(this).addClass("actives");
                var show_target=$(this).attr("data-src");
                show_target="."+show_target;
                var show_contents=$(show_target).parent().children();
                show_contents.each(function () {
                    if($(this).hasClass("thehead-bg-box-txt")) {
                        $(this).css({"display": "none"});
                    }
                });
                $(show_target).css({"display":"block"});
                $(show_target).find('img').each(function () {
                    if($(this).attr('src')=='https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1693192080,4286726362&fm=26&gp=0.jpg'){
                        $(this).attr('src',$(this).attr('data-src'));
                    }
                })
            });
            $(".thehead-title-ul").find('li').mouseenter(function () {
                $(this).find('img').each(function () {
                    if($(this).attr('src')=='https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1693192080,4286726362&fm=26&gp=0.jpg'){
                        $(this).attr('src',$(this).attr('data-src'));
                    }
                })
            });
            $(".window-img").click(function(){
                $(".window-warp").css("display","none");
            })

            $(".zixunwz").click(function(){
                $(this).css("color","#ccc");
            })
            $(".zixunli").click(function(){
                $(this).css("color","#ccc");
            })

            var swiper = new Swiper('.swiper1', {		//swiper2		浠婃棩鐗逛环
                slidesPerView: 6,
                spaceBetween: 0,
                autoplay: {
                    delay:2500,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: '.right1',	//right
                    prevEl: '.left1',	//left
                },
                // Enable debugger
                debugger: true,
            });

            var swiper2 = new Swiper('.swiper2', {		//swiper2		banner
                spaceBetween: 30,
                slidesPerView: 1,
                centeredSlides: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
            });
        }
    })
    // vm.load();


});