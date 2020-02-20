var reg=new RegExp('@{{','g');
var html=$('#app').html();
html=html.replace(reg,'{{');
var body=document.getElementById("app");
body.innerHTML=html;