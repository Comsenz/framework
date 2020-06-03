<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Qcloud\Services;

use Illuminate\Support\Arr;
use TencentCloud\Ms\v20180408\MsClient;
use TencentCloud\Ms\v20180408\Models\DescribeUserBaseInfoInstanceRequest;

class MsService extends AbstractService
{

    const ENDPOINT = 'ms.tencentcloudapi.com';

    const REGION = '';

    protected function getClient()
    {
        return new MsClient($this->cred, self::REGION);
    }

    protected function setEndpoint()
    {
        return self::ENDPOINT;
    }

    public function MsUserInfo()
    {
        $clientRequest = new DescribeUserBaseInfoInstanceRequest();
        $clientRequest->fromJsonString('{}');
        return $this->client->DescribeUserBaseInfoInstance($clientRequest)->serialize();
    }


}
