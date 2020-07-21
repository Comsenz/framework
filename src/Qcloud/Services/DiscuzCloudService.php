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

use GuzzleHttp\Client;

class DiscuzCloudService
{
    protected $config;

    protected $httpClient;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkVersion($body = '')
    {
        return $this->getHttpClient()->request('GET', 'cloud/version');
    }

    /**
     * @param string $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function report($body = '')
    {
        return $this->getHttpClient()->requestAsync('POST', 'cloud/register', [
            'json' => $body
        ]);
    }

    /**
     * @param string $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function qcloudReport($body = '')
    {
        return $this->getHttpClient()->requestAsync('POST', 'cloud/qcloud', [
            'json' => $body
        ]);
    }

    protected function getHttpClient()
    {
        return $this->httpClient ?? $this->httpClient = new Client($this->config);
    }
}
