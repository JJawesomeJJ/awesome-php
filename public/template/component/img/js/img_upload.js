var target="";
$(document).ready(function () {
    var height=$(".img_upload").attr("data-src");
    target=$(".img_upload").attr("data-target");
    $(".img_upload").css("width",height);
    $(".img_upload").css("height",height);
    $("#img_file_container").change(function () {
        change_head();
    })
});
function change_head(){
    var file = document.getElementById("img_file_container");
    var img = file.files[0];
    if(img){
        var url = URL.createObjectURL(img);
        var base64 = this.blobToDataURL(img,function(base64Url) {
            $(target).attr("src",base64Url);
            $("#img_base64").val(base64Url);
        })
    }
}
function blobToDataURL(blob,cb) {
    let reader = new FileReader();
    reader.onload = function (evt) {
        var base64 = evt.target.result;
        cb(base64)
    };
    reader.readAsDataURL(blob);
}