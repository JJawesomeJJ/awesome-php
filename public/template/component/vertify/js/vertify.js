function Slidevertify(success_call_back,fail_call_back) {
    $(document).ready(function () {
        init();
    });
    var is_load = false;
    function init() {
        $(".vertify").css("display","block");
        document.onmousemove = function (e) {
            var ev = e || event;
            ev.cancelBubble = true;
            ev.returnValue = false;
        }//去除浏览器默认的拖拽事件
        var roate_angle = 360;
        $(".refresh").click(function () {
            $(this).rotate({animateTo: roate_angle}, 100);
            roate_angle = roate_angle + 360;
            reload();
        });
        $(".guanbi").click(function () {
            $(".vertify").slideUp(300);
        });
        if (!is_pc()) {
            slide_btn();
        }
        else {
            tele_slide();
        }
    }
    $(".vertify_container").css("background-image", "url(" + "/vertify?" + Date.parse(new Date()) + ")");
    function vertify_x(x) {
        $.ajax({
            type: "POST",
            url: "/vertify/silde/x",
            data: {x: x},
            dataType: "json",
            success: function (res) {
                if (res["code"] == 200) {
                    $("#show_result").attr("class", "iconfont icon-duigou1");
                    $("#show_result").css("color", "blue");
                    $(".result_logo").css("display", "block");
                    $(".vertify").fadeOut(300);
                    success_call_back();
                }
                else {
                    $("#show_result").attr("class", "iconfont icon-cuowutishitianchong");
                    $("#show_result").css("color", "red");
                    $(".result_logo").css("display", "block");
                    fail_call_back();
                    reload();
                    is_load = false;
                }
            },
            error: function (error) {
            }
        });
    }
    function is_pc() {
        var sUserAgent = navigator.userAgent.toLowerCase();
        var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
        var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
        var bIsMidp = sUserAgent.match(/midp/i) == "midp";
        var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
        var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
        var bIsAndroid = sUserAgent.match(/android/i) == "android";
        var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
        var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";
        if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {
            return false;
        } else {
            return true;
        }
    }
    function load() {
        var img_info = eval('(' + getCookies("vertify_code_drop") + ')');
        $(".slide_img").css("background-image", "url(" + img_info["src"] + ")");
        var height = Number(img_info["height"]) / 1.54;
        $(".slide_img").css("top", height);
        $(".slide_img").css("display", "block");
    }

    function reload() {
        $(".vertify_container").css("background-image", "url(" + "/vertify?" + Date.parse(new Date()) + ")");
        $(".slide_img").css("display", "none");
        $(".slide_bar_container").animate({"left": "-260px"}, 300);
        $(".slide_img").css("left", "0px");
        $(".slide_img").css("display", "none");
    }
    function slide_btn() {
        var start_x;
        var move_end_x
        //var start_y;
        var hasTouch = 'ontouchstart' in window,
            startEvent = hasTouch ? 'touchstart' : 'mousedown',
            moveEvent = hasTouch ? 'touchmove' : 'mousemove',
            endEvent = hasTouch ? 'touchend' : 'mouseup',
            cancelEvent = hasTouch ? 'touchcancel' : 'mouseup';
        $(".slide_bar_btn").on(startEvent, function (e) {
            if (is_load == false) {
                load();
                is_load = true;
            }
            //e.preventDefault();
            if (!is_pc()) {
                start_x = e.originalEvent.changedTouches[0].pageX;
            } else {
                start_x = e.originalEvent.clientX;
            }
            //start_y = e.originalEvent.changedTouches[0].pageY;
        });

        $(".slide_bar_btn").on(moveEvent, function (e) {
            if (is_load == false) {
                console.log("return");
                return;
            }
            //e.preventDefault();
            if (is_pc()) {
                move_end_x = e.originalEvent.clientX;
            } else {
                move_end_x = e.originalEvent.changedTouches[0].pageX;
            }
            //moveEndY = e.originalEvent.changedTouches[0].pageY;
            var left = $(".slide_bar_container").css("left");
            left = left.replace("px", "");
            left = (Number(move_end_x) - Number(start_x) + Number(left));
            var slide_img_left = $(".slide_img").css("left");
            slide_img_left = slide_img_left.replace("px", "");
            slide_img_left = (Number(move_end_x) - Number(start_x) + Number(slide_img_left));
            if (left < 0 && left > -260) {
                left = left + "px";
                slide_img_left = slide_img_left + "px";
                $(".slide_bar_container").css("left", left);
                $(".slide_img").css("left", slide_img_left);
            }
            start_x = move_end_x;
        });
        $(".slide_bar_btn").on(endEvent, function (e) {
            if (is_load == false) {
                return;
            }
            x = (Number($(".slide_bar_container").css("left").replace("px", "")) + 260) * 1.673076;
            vertify_x(x);
        });
        $(".slide_bar_btn").on(cancelEvent, function (e) {
            is_load = false;
        });
        $(".slide_bar_btn").mouseleave(function () {
            if (is_load == false) {
                return;
            }
            x = (Number($(".slide_bar_container").css("left").replace("px", "")) + 260) * 1.673076;
            vertify_x(x);
            is_load = false;
        });
    }

    function tele_slide() {
        var start_x;
        var move_end_x
        //var start_y;
        var hasTouch = 'ontouchstart' in window,
            startEvent = hasTouch ? 'touchstart' : 'mousedown',
            moveEvent = hasTouch ? 'touchmove' : 'mousemove',
            endEvent = hasTouch ? 'touchend' : 'mouseup',
            cancelEvent = hasTouch ? 'touchcancel' : 'mouseup';
        $(".slide_bar_btn").on(startEvent, function (e) {
            console.log("load");
            $(".vertify_container").slideDown(200);
            if (is_load == false) {
                load();
                is_load = true;
            }
            //e.preventDefault();
            if (!is_pc()) {
                start_x = e.originalEvent.changedTouches[0].pageX;
            } else {
                start_x = e.originalEvent.clientX;
            }
            //start_y = e.originalEvent.changedTouches[0].pageY;
        });
        $("body").on(moveEvent, function (e) {
            if (is_load == false) {
                return;
            }
            //e.preventDefault();
            if (is_pc()) {
                move_end_x = e.originalEvent.clientX;
            } else {
                move_end_x = e.originalEvent.changedTouches[0].pageX;
            }
            //moveEndY = e.originalEvent.changedTouches[0].pageY;
            var left = $(".slide_bar_container").css("left");
            left = left.replace("px", "");
            left = (Number(move_end_x) - Number(start_x) + Number(left));
            var slide_img_left = $(".slide_img").css("left");
            slide_img_left = slide_img_left.replace("px", "");
            slide_img_left = (Number(move_end_x) - Number(start_x) + Number(slide_img_left));
            if (left < 0 && left > -260) {
                left = left + "px";
                slide_img_left = slide_img_left + "px";
                $(".slide_bar_container").css("left", left);
                $(".slide_img").css("left", slide_img_left);
            }
            start_x = move_end_x;
        });
        $("body").on(endEvent, function (e) {
            if (is_load == false) {
                return;
            }
            x = (Number($(".slide_bar_container").css("left").replace("px", "")) + 260) * 1.673076;
            vertify_x(x);
        });
        $("body").on(cancelEvent, function (e) {
            is_load = false;
        });
        $("body").mouseleave(function () {
            if (is_load == false) {
                return;
            }
            x = (Number($(".slide_bar_container").css("left").replace("px", "")) + 260) * 1.673076;
            vertify_x(x);
            is_load = false;
        });
    }
    this.show=function show() {
        reload();
        $(".vertify").css("display","block");
    }
}