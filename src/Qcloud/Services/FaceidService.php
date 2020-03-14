<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Qcloud\Services;

use Illuminate\Support\Arr;
use TencentCloud\Faceid\V20180301\FaceidClient;
use TencentCloud\Faceid\V20180301\Models\IdCardVerificationRequest;

class FaceidService extends AbstractService
{
    protected function getClient()
    {
        return new FaceidClient($this->cred, Arr::get($this->config, 'qcloud_faceid_region', 'ap-beijing'), $this->clientProfile);
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
