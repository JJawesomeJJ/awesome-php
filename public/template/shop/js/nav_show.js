$(document).ready(function () {
    var url=window.location.href;
    full_url=window.location.href;
    if(url.indexOf('?')>0){
        index=url.indexOf('?')
        url=url.substring(0,index)
        full_url=window.location.href.substring(0,window.location.href.indexOf('?'));
        console.log(full_url);
    }
    url=url.replace('https://','').replace('http://','').replace('//','/');
    var start_index=url.indexOf('/');
    url=url.substring(start_index,url.length);
    $(".side-navbar").find('a').each(function () {
        if($(this).attr('href')==full_url||$(this).attr('href')==url){
            $(this).parent().addClass('active');
        }
    })
})