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
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;

abstract class AbstractService
{
    const ENDPOINT = '';

    const REGION = '';

    protected $region;

    protected $config;

    protected $httpProfile;

    protected $clientProfile;

    protected $client;

    public function __construct($config)
    {
        $this->config = $config;

        $this->cred = new Credential(Arr::get($config, 'qcloud_secret_id'), Arr::get($config, 'qcloud_secret_key'), Arr::get($config, 'qcloud_token', ''));

        $this->httpProfile = new HttpProfile();
        $this->setEndpoint();

        $this->clientProfile = new ClientProfile();
        $this->clientProfile->setHttpProfile($this->httpProfile);

        $this->client = $this->getClient();
    }

    abstract protected function getClient();

    abstract protected function setEndpoint();
}
