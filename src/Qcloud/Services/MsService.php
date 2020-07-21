<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Discuz\Qcloud\Services;

use TencentCloud\Ms\V20180408\MsClient;
use TencentCloud\Ms\V20180408\Models\DescribeUserBaseInfoInstanceRequest;

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
