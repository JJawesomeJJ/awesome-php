function sha256(s){

    var chrsz   = 8;
    var hexcase = 0;

    function safe_add (x, y) {
        var lsw = (x & 0xFFFF) + (y & 0xFFFF);
        var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
        return (msw << 16) | (lsw & 0xFFFF);
    }

    function S (X, n) { return ( X >>> n ) | (X << (32 - n)); }
    function R (X, n) { return ( X >>> n ); }
    function Ch(x, y, z) { return ((x & y) ^ ((~x) & z)); }
    function Maj(x, y, z) { return ((x & y) ^ (x & z) ^ (y & z)); }
    function Sigma0256(x) { return (S(x, 2) ^ S(x, 13) ^ S(x, 22)); }
    function Sigma1256(x) { return (S(x, 6) ^ S(x, 11) ^ S(x, 25)); }
    function Gamma0256(x) { return (S(x, 7) ^ S(x, 18) ^ R(x, 3)); }
    function Gamma1256(x) { return (S(x, 17) ^ S(x, 19) ^ R(x, 10)); }

    function core_sha256 (m, l) {
        var K = new Array(0x428A2F98, 0x71374491, 0xB5C0FBCF, 0xE9B5DBA5, 0x3956C25B, 0x59F111F1, 0x923F82A4, 0xAB1C5ED5, 0xD807AA98, 0x12835B01, 0x243185BE, 0x550C7DC3, 0x72BE5D74, 0x80DEB1FE, 0x9BDC06A7, 0xC19BF174, 0xE49B69C1, 0xEFBE4786, 0xFC19DC6, 0x240CA1CC, 0x2DE92C6F, 0x4A7484AA, 0x5CB0A9DC, 0x76F988DA, 0x983E5152, 0xA831C66D, 0xB00327C8, 0xBF597FC7, 0xC6E00BF3, 0xD5A79147, 0x6CA6351, 0x14292967, 0x27B70A85, 0x2E1B2138, 0x4D2C6DFC, 0x53380D13, 0x650A7354, 0x766A0ABB, 0x81C2C92E, 0x92722C85, 0xA2BFE8A1, 0xA81A664B, 0xC24B8B70, 0xC76C51A3, 0xD192E819, 0xD6990624, 0xF40E3585, 0x106AA070, 0x19A4C116, 0x1E376C08, 0x2748774C, 0x34B0BCB5, 0x391C0CB3, 0x4ED8AA4A, 0x5B9CCA4F, 0x682E6FF3, 0x748F82EE, 0x78A5636F, 0x84C87814, 0x8CC70208, 0x90BEFFFA, 0xA4506CEB, 0xBEF9A3F7, 0xC67178F2);
        var HASH = new Array(0x6A09E667, 0xBB67AE85, 0x3C6EF372, 0xA54FF53A, 0x510E527F, 0x9B05688C, 0x1F83D9AB, 0x5BE0CD19);
        var W = new Array(64);
        var a, b, c, d, e, f, g, h, i, j;
        var T1, T2;

        m[l >> 5] |= 0x80 << (24 - l % 32);
        m[((l + 64 >> 9) << 4) + 15] = l;

        for ( var i = 0; i<m.length; i+=16 ) {
            a = HASH[0];
            b = HASH[1];
            c = HASH[2];
            d = HASH[3];
            e = HASH[4];
            f = HASH[5];
            g = HASH[6];
            h = HASH[7];

            for ( var j = 0; j<64; j++) {
                if (j < 16) W[j] = m[j + i];
                else W[j] = safe_add(safe_add(safe_add(Gamma1256(W[j - 2]), W[j - 7]), Gamma0256(W[j - 15])), W[j - 16]);

                T1 = safe_add(safe_add(safe_add(safe_add(h, Sigma1256(e)), Ch(e, f, g)), K[j]), W[j]);
                T2 = safe_add(Sigma0256(a), Maj(a, b, c));

                h = g;
                g = f;
                f = e;
                e = safe_add(d, T1);
                d = c;
                c = b;
                b = a;
                a = safe_add(T1, T2);
            }

            HASH[0] = safe_add(a, HASH[0]);
            HASH[1] = safe_add(b, HASH[1]);
            HASH[2] = safe_add(c, HASH[2]);
            HASH[3] = safe_add(d, HASH[3]);
            HASH[4] = safe_add(e, HASH[4]);
            HASH[5] = safe_add(f, HASH[5]);
            HASH[6] = safe_add(g, HASH[6]);
            HASH[7] = safe_add(h, HASH[7]);
        }
        return HASH;
    }

    function str2binb (str) {
        var bin = Array();
        var mask = (1 << chrsz) - 1;
        for(var i = 0; i < str.length * chrsz; i += chrsz) {
            bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (24 - i%32);
        }
        return bin;
    }

    function Utf8Encode(string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    }

    function binb2hex (binarray) {
        var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
        var str = "";
        for(var i = 0; i < binarray.length * 4; i++) {
            str += hex_tab.charAt((binarray[i>>2] >> ((3 - i%4)*8+4)) & 0xF) +
                hex_tab.charAt((binarray[i>>2] >> ((3 - i%4)*8  )) & 0xF);
        }
        return str;
    }

    s = Utf8Encode(s);
    return binb2hex(core_sha256(str2binb(s), s.length * chrsz));
}
function refresh_code() {
    var code=document.querySelector("#vertify_code");
    code.src="http://www.titang.shop/code/code"+"?"+new Date().getTime();
}
function getCookies(cookieName) {
    var strCookie = document.cookie;
    if(document.cookie=="")
    {
        if(localStorage.getItem(cookieName)!=null)
        {
            return localStorage.getItem(cookieName);
        }
        else {
            return "";
        }
    }
    var arrCookie = strCookie.split("; ");
    for(var i = 0; i < arrCookie.length; i++){
        var arr = arrCookie[i].split("=");
        if(cookieName == arr[0]){
            return decodeURIComponent(arr[1]);
        }
    }
    return "";
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
function get_current_time() {//获取当前时间
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
    var strDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
        + " " + date.getHours() + seperator2 + date.getMinutes()
        + seperator2 + date.getSeconds();
    return currentdate;
}
function get_html_width() {
    return document.getElementsByTagName('html')[0].getBoundingClientRect().width;
}
function get_diffTime(start_time,end_time) {
    var return_string="";
    var diff=Math.abs(end_time-start_time);
    var day=parseInt(diff/(60*60*24));
    var res=diff%(60*60*24);
    var hour=parseInt(res/(60*60));
    res=res%(60*60);
    var minutes=parseInt(res/(60));
    res=res%60;
    var sencond=res/1;
    if(day>0){
        return_string=return_string+day+"天";
    }
    if(hour>0){
        return_string=return_string+hour+"小时";
    }
    if(minutes>0){
        return_string=return_string+minutes+"分"
    }
    return return_string+sencond+"秒";
}
function is_equal(a,b){
    //如果a和b本来就全等
    if(a===b){
        //判断是否为0和-0
        return a !== 0 || 1/a ===1/b;
    }
    //判断是否为null和undefined
    if(a==null||b==null){
        return a===b;
    }
    //接下来判断a和b的数据类型
    var classNameA=toString.call(a),
        classNameB=toString.call(b);
    //如果数据类型不相等，则返回false
    if(classNameA !== classNameB){
        return false;
    }
    //如果数据类型相等，再根据不同数据类型分别判断
    switch(classNameA){
        case '[object RegExp]':
        case '[object String]':
            //进行字符串转换比较
            return '' + a ==='' + b;
        case '[object Number]':
            //进行数字转换比较,判断是否为NaN
            if(+a !== +a){
                return +b !== +b;
            }
            //判断是否为0或-0
            return +a === 0?1/ +a === 1/b : +a === +b;
        case '[object Date]':
        case '[object Boolean]':
            return +a === +b;
    }
    //如果是对象类型
    if(classNameA == '[object Object]'){
        //获取a和b的属性长度
        var propsA = Object.getOwnPropertyNames(a),
            propsB = Object.getOwnPropertyNames(b);
        if(propsA.length != propsB.length){
            return false;
        }
        for(var i=0;i<propsA.length;i++){
            var propName=propsA[i];
            //如果对应属性对应值不相等，则返回false
            if(a[propName] !== b[propName]){
                return false;
            }
        }
        return true;
    }
    //如果是数组类型
    if(classNameA == '[object Array]'){
        if(a.toString() == b.toString()){
            return true;
        }
        return false;
    }
}
function object_content_equal(object1,object2) {
    var key=Object.keys(object1);
    for (var i of key) {
        if(object1[i]!=object2[i]){
            return false;
        }
    }
    return true;
}
function object_content_copy(object1) {
    var key=Object.keys(object1);
    var object2={};
    for (var i of key) {
        object2[i]=object1[i];
    }
    return object2;
}

/**
 * @description 与Android客户端共享cookie
 * @param name
 * @param value
 * @param timestamp 到期的时间
 */
// 设置cookie
function setCookie(name, value, timestamp) {
    console.log("cookies has been load");
    var expires = "";
    if (timestamp != 0 ) {      //设置cookie生存时间
        var date = new Date();
        date.setTime(timestamp);
        expires = "; expires="+date.toGMTString();
    }
    document.cookie = name+"="+value+expires+"; path=/";   //转码并赋值
}

/**
 * @description 将url参数转化为对象
 * @param params_string
 * @returns {*}
 */
function params_parse_object(params_string) {
    var params={};
    var params_list=params_string.split("&");
    for (var i of params_list){
        var param=i.split("=");
        params[param[0]]=decodeURI(param[1])
    }
    return params;
}
function object_parse_url(params) {
    var str="";
    for (var i in params){
        str=str+i+"="+params[i]+"&";
    }
    return "?"+str.substring(0,str.length-1);
}
var request=function () {
    var object_=null;
    var then_=null;
    var catch_=null;
    var pre_load=[];
    this.get=function (url,params) {
        for (var i of pre_load){
            i(url,params);
        }
        var fun1=then_;
        then_=null;
        var fun2=catch_;
        catch_=null;
        object_=axios.get(url+object_parse_url(params));
        return this;
    };
    this.post=function (url,params={}) {
        var fun1=then_;
        then_=null;
        var fun2=catch_;
        catch_=null;
        for (var i of pre_load){
            i(url,params);
        }
        object_=axios.post(url,params);
        return object_;
    };
    this.then=function (fun) {
        object_.then(function (res) {
            fun(res)
        });
        return this;
    };
    this.catch=function (fun) {
        object_.catch(function (err) {
            fun(err)
        });
        return this;
    };
    this.bind_pre=function (fun) {
        pre_load.push(fun)
    };
};
var Request=new request();
Request.bind_pre(function () {

});
String.prototype.format = function(args) {
    var result = this;
    if (arguments.length > 0) {
        if (arguments.length == 1 && typeof (args) == "object") {
            for (var key in args) {
                if(args[key]!=undefined){
                    var reg = new RegExp("({" + key + "})", "g");
                    result = result.replace(reg, args[key]);
                }
            }
        }
        else {
            for (var i = 0; i < arguments.length; i++) {
                if (arguments[i] != undefined) {
                    var reg= new RegExp("({)" + i + "(})", "g");
                    result = result.replace(reg, arguments[i]);
                }
            }
        }
    }
    return result;
};

function rand(min,max) {
    return Math.floor(Math.random()*(max-min+1)+min);
}