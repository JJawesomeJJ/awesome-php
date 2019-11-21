$(document).ready(function () {
    var iframe = document.getElementById('frame');
    iframe.onload = function() {
        var newValue=iframe.getAttribute("src");
        var url_list=newValue.split("/");
        var basename=url_list[url_list.length-1].split('.')[0];
        basename=decodeURI(basename);
        $("#sidebarnav").find("a").each(function () {
            if($(this).text()==basename){
                $(this).parent().addClass("select")
            }else {
                if($(this).parent().hasClass("select")){
                    $(this).parent().removeClass("select");
                }
            }
        })
    }
})
