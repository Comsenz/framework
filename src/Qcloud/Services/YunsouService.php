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

    public function index($params)
    {
        $req = new DataManipulationRequest();
        $req->fromJsonString(json_encode($params));
        return $this->getClient()->DataManipulation($req)->serialize();
    }

    public function search($params)
    {
        $req = new DataSearchRequest();
        $req->fromJsonString(json_encode($params));
        return $this->getClient()->DataSearch($req)->serialize();
    }
}
