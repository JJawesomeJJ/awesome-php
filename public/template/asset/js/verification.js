function verification(){
    function disapper_error() {
        $(document).ready(function () {
            $("input").focus(function () {
                if($(this).hasClass("input-warnning")){
                    $(this).removeClass("input-warnning")
                }
            });
            $("input").blur(function () {
                if (typeof ($(this).attr("reqiure")) != "undefined"){
                    var user_input={};
                    user_input[$(this).attr("name")] = init_variable($(this).attr("reqiure"), $(this).val());
                    start(user_input)
                }
            });
            $('input').on('input propertychange', function() {
                if (typeof ($(this).attr("reqiure")) != "undefined"){
                    var user_input={};
                    user_input[$(this).attr("name")] = init_variable($(this).attr("reqiure"), $(this).val());
                    start(user_input)
                }
            });
        })
    }
    disapper_error();
    function init_user_input() {
        user_input={};
        $("input").each(function () {
            if (typeof ($(this).attr("reqiure")) != "undefined") {
                if ($(this).attr("name") != "") {
                    user_input[$(this).attr("name")] = init_variable($(this).attr("reqiure"), $(this).val());
                }
            }
        });
        return user_input;
    }
    function init_variable(str,value) {
        var rules={};
        var key_params=str.split("|");
        for (var i of key_params){
            var error_message=null;
            var index=i.indexOf("-");
            if(index!=-1){
                error_message=i.substring(index+1,i.length)
                i=i.substring(0,index)
            }
            var params=i.split(":");
            var name=params[0];
            if(params.length==1){
                rules[name]={"params":[value],"error_message":error_message};
            }
            else {
                var params_list=params[1].split(",");
                params_list.unshift(value);
                rules[name]={"params":params_list,"error_message":error_message};
            }
        }
        return rules;
    }
    function start(user_input) {
        var error_info=[];
        for (var name in user_input){
            for (var fun_name in user_input[name]) {
                if(fun_name==''){
                    continue;
                }
                var fun=eval(fun_name);
                if(fun_name=="asyn"){
                    user_input[name][fun_name]["params"].unshift(name);
                    user_input[name][fun_name]["params"][4]=eval(user_input[name][fun_name]["params"][4]).apply()
                    fun.apply(null,user_input[name][fun_name]["params"]);
                    continue;
                }
                var result=fun.apply(null,user_input[name][fun_name]["params"]);
                if(result!==true){
                    if(user_input[name][fun_name]["error_message"]!=null){
                        result=user_input[name][fun_name]["error_message"];
                    }
                    error_handle(name,result);
                    error_info.push({"name":name,"message":result});
                }
                else {
                    seccess_handle(name)
                }
            }
        }
        return error_info;
    }
    function seccess_handle(name) {
        $("input[name="+name+"]").addClass("input-success");
        if($("input[name="+name+"]").hasClass("input-warnning")){
            $("input[name="+name+"]").removeClass("input-warnning");
        }
        $("input[name="+name+"]").parent().children().each(function () {
            if($(this).hasClass("input-tip")){
                $(this).remove();
            }
        })
    }
    function error_handle(name,result) {
        if(!$("input[name="+name+"]").hasClass("input-warnning")){
            $("input[name="+name+"]").addClass("input-warnning");
            $("input[name="+name+"]").removeClass("input-success");
        }
        $("input[name="+name+"]").parent().children().each(function () {
            if($(this).hasClass("input-tip")){
                $(this).remove();
            }
        })
        $("input[name="+name+"]").parent().append("<label class='input-tip' style='float: left;color: red;font-size: 10px;font-weight: lighter;margin-top: 2px;'>"+result+"</label>");
    }
    function noempty(str,test) {
        if(str.trim()==""||str==null){
            return "输入不可以为空";
        }
        return true;
    }
    function email(value) {
        var mailReg = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
        if(mailReg.test(value)){
            return true;
        }else{
            return "请输入正确的邮箱号";
        }
    }
    function tel(phonevalue){
        var phoneReg = /^1[3-578]\d{9}$/;
        if(phoneReg.test(phonevalue)){
            return true;
        }else{
            return "请输入正确的手机格式";
        }
    }
    function file(object,type) {

    }
    function comfirm(value,id) {
        if(value==$("#"+id).val()){
            return true;
        }
        else {
            return "请确保输入一致"
        }
    }
    function asyn(name,input,url,type,data,fail_info=null) {
        $.ajax({
            url:url.replace("**",":"), //请求的url地址
            data:data, //参数值
            type:type, //请求方式
            success:function(req){
                if(req===true){
                    seccess_handle(name)
                }else {
                    if(fail_info!=null){
                        error_handle(name,fail_info)
                    }
                    else {
                        error_handle(name,req)
                    }
                }
            },
            error:function(err){
                console.error(err)
            }
        });
    }
    function num(val) {
        if(val === "" || val ==null){
            return "请输入数字"
        }
        if(!isNaN(val)){
            return true;
        }
        else{
            return "请输入数字"
        }
    }
    function len_min(val,min){
        if(val.length<min){
            return "最少输入"+min+"位";
        }
        return true;
    }
    function len_max(val,min){
        if(val.length>max){
            return "最多输入"+min+"位";
        }
        return true;
    }
    function min(val,min) {
        if(!num(val)){
            return num(val);
        }
        if(val<min){
            return "最少输入"+min;
        }
        return true;
    }
    function max(val,max) {
        if(!num(val)){
            return num(val);
        }
        if(val>max){
            return "最大输入"+max;
        }
        return true;
    }
    // function init_check() {
    //     $(document).ready(function () {
    //         $("input").blur(function () {
    //             if ($(this).attr("name") != "") {
    //                 user_input[$(this).attr("name")] = init_variable($(this).attr("reqiure"), $(this).val());
    //             }
    //             start();
    //         })
    //     })
    // }
    this.check=function(sucess=null,fail=null){
        var error_info=start(init_user_input());
        if(error_info.length==0){
            if(sucess==null){
                return true;
            }
            sucess();
        }
        else {
            if(fail==null){
                return error_info;
            }
            fail(error_info)
        }
    }
};
var vertify=new verification();
function array_key_value(arr,key) {
    var result=[];
    for (var i of arr){
        result.push(i[key]);
    }
    return result;
}
function easy_check() {
    var result=vertify.check();
    if(result===true){
        console.log(result)
        return true;
    }else {
        show_error(array_key_value(result,"message"));
        return false;
    }
}
