function check() {
    var password1=document.querySelector("#password");
    var password=password1.value;
    var password_sure=document.querySelector("#password_sure").value;
    if(password.length<6){
        alert("亲！密码要大于6位哦！");
        return false;
    }
    if(password!=password_sure){
        alert("密码不一致哦！");
        return false;
    }
    password1.value=sha256(password1.value);
    return true;
}