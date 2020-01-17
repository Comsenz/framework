<?php


namespace Discuz\Qcloud\Services;


use TencentCloud\Cms\V20190321\Models\ImageModerationRequest;
use TencentCloud\Faceid\V20180301\FaceidClient;
use TencentCloud\Faceid\V20180301\Models\IdCardVerificationRequest;

class FaceidService extends AbstractService
{

    protected function getClient()
    {
        return new FaceidClient($this->cred, '', $this->clientProfile);
    }

    protected function setEndpoint()
    {
        return 'faceid.tencentcloudapi.com';
    }

    public function idCardVerification($idCard, $name)
    {
        $clientRequest = new IdCardVerificationRequest();
        $clientRequest->fromJsonString(json_encode(['IdCard' => $idCard, 'Name' => $name]));
        return $this->client->IdCardVerification($clientRequest)->serialize();
    }
}
