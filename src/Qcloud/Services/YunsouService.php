<?php


namespace Discuz\Qcloud\Services;


use TencentCloud\Yunsou\V20180504\Models\DataManipulationRequest;
use TencentCloud\Yunsou\V20180504\Models\DataSearchRequest;
use TencentCloud\Yunsou\V20180504\YunsouClient;

class YunsouService extends AbstractService
{

    protected function getClient()
    {
        return new YunsouClient($this->cred, '', $this->clientProfile);
    }

    protected function setEndpoint()
    {
        return 'yunsou.tencentcloudapi.com';
    }

    public function index($params) {
        $req = new DataManipulationRequest();
        $req->fromJsonString(json_encode($params));
        return $this->getClient()->DataManipulation($req)->serialize();
    }

    public function search($params) {
        $req = new DataSearchRequest();
        $req->fromJsonString(json_encode($params));
        return $this->getClient()->DataSearch($req)->serialize();
    }
}
