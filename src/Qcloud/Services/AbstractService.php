<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Qcloud\Services;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;

abstract class AbstractService
{
    const ENDPOINT = '';

    const REGION = '';

    protected $cred;

    protected $httpProfile;

    protected $clientProfile;

    protected $client;

    public function __construct($config)
    {
        $this->cred = new Credential($config['qcloud_secret_id'], $config['qcloud_secret_key'], '');

        $this->httpProfile = new HttpProfile();
        $this->setEndpoint();

        $this->clientProfile = new ClientProfile();
        $this->clientProfile->setHttpProfile($this->httpProfile);

        $this->client = $this->getClient();
    }

    abstract protected function getClient();

    abstract protected function setEndpoint();
}
