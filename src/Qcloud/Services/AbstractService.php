<?php


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

    public function __construct($secretId, $secretKey, $token = null)
    {
        $this->cred = new Credential($secretId, $secretKey, $token = null);


        $this->httpProfile = new HttpProfile();
        $this->setEndpoint();

        $this->clientProfile = new ClientProfile();
        $this->clientProfile->setHttpProfile($this->httpProfile);

        $this->client = $this->getClient();
    }

    abstract protected function getClient();

    abstract protected function setEndpoint();
}
