<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Qcloud\Services;

use GuzzleHttp\Client;

class CheckVersionService
{
    protected $config;

    protected $httpClient;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $body
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function checkVersion($body = '')
    {
        return $this->getHttpClient()->request('POST', 'qcloud/version', [
            'json' => $body,
        ]);
    }

    protected function getHttpClient()
    {
        return $this->httpClient ?? $this->httpClient = new Client($this->config);
    }
}
