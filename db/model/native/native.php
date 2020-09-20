<?php


namespace db\model\native;


class native
{
    protected $native_type=[
        [
            "name"=>"颜值",
            "url"=>"https://ss1.bdstatic.com/70cFvXSh_Q1YnxGkpoWK1HF6hhy/it/u=2615516231,2905958076&fm=26&gp=0.jpg",
            "type"=>"beautiful",
        ],
        [
            "name"=>"英雄联盟",
            "url"=>"https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2337237933,2000847306&fm=26&gp=0.jpg",
            "type"=>"lol",
        ],
        [
            "name"=>"绝地求生",
            "url"=>"https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2110681502,1535098269&fm=26&gp=0.jpg",
            "type"=>"eat_chicken",
        ],
        [
            "name"=>"王者荣耀",
            "url"=>"https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=202548807,2558761829&fm=26&gp=0.jpg",
            "type"=>"wangzherongyao",
        ],
        [
            "name"=>"DNF",
            "url"=>"https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1740738040,1113126080&fm=26&gp=0.jpg",
            "type"=>"dnf",
        ],
        [
            "name"=>"主机游戏",
            "url"=>"https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=2779191552,1421813831&fm=26&gp=0.jpg",
            "type"=>"games",
        ],
        [
            "name"=>"穿越火线",
            "url"=>"https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1571160934976&di=331d2828b4b328f7757d8a02841329d5&imgtype=0&src=http%3A%2F%2Fkascdn.kascend.com%2Fjellyfish%2Fspace%2Ftopic%2F170203%2F1486114235309.jpg",
            "type"=>"cf",
        ],
        [
            "name"=>"和平精英",
            "url"=>"https://ss0.baidu.com/6ONWsjip0QIZ8tyhnq/it/u=3460768464,3742418663&fm=58&bpow=400&bpoh=400",
            "type"=>"hepingjingying"
        ],
        [
            "name"=>"DOTA",
            "url"=>"https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=3134454663,859220742&fm=26&gp=0.jpg",
            "type"=>"dota2"
        ]
    ];

    /**
     * 获取在线的直播的类型
     * @return array
     */
    public function get_native_type(){
        return $this->native_type;
    }
}