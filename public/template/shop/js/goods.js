$(document).ready(function () {
    new Vue({
        el:"#app",
        data:{
            children:{

            },
            show_key:null,
            user_info:null
        },
        created(){
            this.init_user();
        },
        mounted(){
            this.init();
        },
        methods: {
            init(){
                var self=this;
                $(".catalog-parent").find('li').hover(function () {
                    var key=$(this).text().trim();
                    if(self.children.hasOwnProperty(key)){
                        if($('.show_catalog').hasClass('col-md-10')){
                            $(this).addClass('col-md-10');
                        }
                        self.show_key=key;
                    }
                    else {
                        axios.post('/shop/categories/children',{name:key}
                        ).then(function (res) {
                            self.children[key]=res['data'];
                            self.show_key=key;
                        })
                    }
                });
                $(".catalog-parent").mouseleave(function () {
                    var self=this;
                    self.show=null;
                    $(".show_catalog").show(300);
                    $(this).hide(300);
                });
                $(".show_catalog").click(function () {
                    $(".catalog-parent").css("display","flex");
                    $(this).hide();
                })
            },
            query(name){
                window.location.href="/shop/goods/list?name="+name;
            },
            init_user(){
                this.user_info=JSON.parse(localStorage.getItem("admin_user_data"));
            }
        }
    });
});