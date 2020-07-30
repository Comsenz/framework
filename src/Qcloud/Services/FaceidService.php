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
