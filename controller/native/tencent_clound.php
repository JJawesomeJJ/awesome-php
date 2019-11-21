<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/17 0017
 * Time: 上午 8:29
 */

namespace controller\native;
use load\auto_load;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Live\V20180801\LiveClient;
use TencentCloud\Live\V20180801\Models\DescribeLiveStreamOnlineListRequest;

class tencent_clound
{
    public function __construct()
    {
        auto_load::load("tencentcloud-sdk-php.TCloudAutoLoader");
    }
    public  function get_online_list(){
        try {
            $cred = new Credential("AKIDFJ9jHaxJI1VkgNcPedPyHNCqIUPWb3Xl", "CdswjKgmcDYfWN0OWTBcnjcGdplRr54x");
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("live.tencentcloudapi.com");
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new LiveClient($cred, "ap-chengdu", $clientProfile);
            $req = new DescribeLiveStreamOnlineListRequest();
            $params = '{}';
            $req->fromJsonString($params);
            $resp = $client->DescribeLiveStreamOnlineList($req);
            return $resp->OnlineInfo;
        }
        catch(TencentCloudSDKException $e) {
           return $e;
        }
    }
}