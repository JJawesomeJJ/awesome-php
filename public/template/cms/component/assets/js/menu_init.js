$(document).ready(function () {
    console.log($(window).width());
    $(".menu-p").click(function () {
        var ele=$(this).parent().find(".menu-level-1-container");
        var height=$(".menu-level-2").height();
        if(!ele.hasClass("container-hover")) {
            ele.addClass("container-hover");
            $(".left-menu").removeClass("left-menu-hover");
            $(this).parent().find(".left-menu").addClass("left-menu-hover");
            $(this).find(".left-narrow").rotate({animateTo: 90});
            var height=(height+8)*ele.find(".menu-level-2").length;
            ele.css("height","0px");
            ele.removeClass("container-none");
            ele.animate({height:Number(height)+"px"},500);
        }else {
            ele.stop();
            ele.removeClass("container-hover");
            $(this).parent().find(".left-menu").removeClass("left-menu-hover");
            $(this).find(".left-narrow").rotate({animateTo: 0});
            ele.addClass("container-none");
        }
    });
    $(".menu-level-2").click(function () {
        $(".menu-level-2").removeClass("menu-hover");
        $(this).addClass("menu-hover");
    });
    $(".menu-hover").parent().addClass("container-hover");
    var ele=$(".container-hover");
    var height=$(".menu-level-2").height();
    ele.parent().find(".left-narrow").rotate({animateTo: 90});
    ele.parent().find(".left-menu").addClass("left-menu-hover");
    height=(height+8)*ele.find(".menu-level-2").length;
    ele.css("height",Number(height)+"px");
});