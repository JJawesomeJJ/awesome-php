$(document).ready(function () {
    var is_send=false;
    $('#user_id').bind('input propertychange', function() {
        is_ok();
    });
    $('#code').bind('input propertychange',function () {
        is_ok();
    })
    function is_ok() {
        if($("#user_id").val().length>0&&$("#code").val().length>=3){
            $("#submit_t").css("background-color","#0cca62");
            is_send=true;
        }
        else {
            $("#submit_t").css("background-color","darkgrey");
            is_send=false;
        }
    }
    $("#submit_t").click(function () {
        if(!is_send){
            return;
        }
        $.ajax({
            type : "post",
            url : "http://39.108.236.127/php/public/index.php/user/reset",
            data:{"user_id":$("#user_id").val(),"code":$("#code").val()},
            dataType : "json",
            success: function (data) {
                if(data.code=="200"){
                    alert("已将魔术连接发往您的邮箱注意查收！")
                }
                else {
                    if(data.message=="code_error"){
                        alert("验证码错误！");
                        refresh_code();
                    }
                    else {
                        alert("用户不存在，或您的输入有误！");
                        refresh_code();
                    }
                }
            },
            error:function (data) {
            }
        });
    })
});