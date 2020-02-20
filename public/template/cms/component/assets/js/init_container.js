$(document).ready(function () {
   init_page();
});
$(window).resize(function () {
    init_page();
})
function init_page() {
    var width=$(".menu").width();
    var left=$(".menu").css("left").replace("px","");
    $(".cms-container").css("width",Number($(window).width()-width-left)+"px");
    $(".cms-container").css("left",width+"px");
}