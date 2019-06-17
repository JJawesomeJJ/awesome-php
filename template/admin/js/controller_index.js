$(document).ready(function () {
    var switch_config=[
        [
            "layui-icon-spread-left",
            "layui-icon-shrink-right"
        ]
    ];
    switch_change();
    function switch_change() {
        $(".j-switch").click(function () {
            var class_name_string=$(this).attr("class");
            var class_list=$(this).attr("class").split(" ");
            var datatarget=$(this).attr("data-target");
            var show_way=$(datatarget).attr("data-show");
            if(typeof(show_way)!="undefined") {
                switch(show_way) {
                    case "left":
                        if($(datatarget).css("display") == "none")
                        {
                            show_left(datatarget);
                        }
                        else
                        {
                            hidden_left(datatarget);
                        }
                        break;
                }
            }
            for(var class_name of class_list){
                for(var switch_ of switch_config){
                    if(switch_[0]==class_name){
                        $(this).attr("class",class_name_string.replace(class_name,switch_[1]));
                        break;
                    }
                    if(switch_[1]==class_name){
                        $(this).attr("class",class_name_string.replace(class_name,switch_[0]));
                        break;
                    }
                }
            }
        });
    }
    function show_left(datatarget) {
        var left=$(datatarget).css("width");
        $(datatarget).css("cssText","display: block!important");
        $(datatarget).css("left","-200px");
        $(datatarget).animate({"left":"0px"},200);
    }
    function hidden_left(datatarget) {
        var left=$(datatarget).css("width");
        $(datatarget).animate({"left":"-200px"},200,function () {
            $(datatarget).css("cssText","display: none!important");
        });
    }
});