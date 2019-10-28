<?php

namespace Discuz\Qcloud\Services;

use TencentCloud\Billing\V20180709\BillingClient;
use TencentCloud\Billing\V20180709\Models\DescribeAccountBalanceRequest;

class BillingService extends AbstractService
{
    const ENDPOINT = 'billing.tencentcloudapi.com';

    public function DescribeAccountBalance() {
        $req = new DescribeAccountBalanceRequest();
        return $this->client->DescribeAccountBalance($req)->serialize();
    }

    protected function getClient()
    {
        return new BillingClient($this->cred, self::REGION, $this->clientProfile);
    }

    protected function setEndpoint()
    {
        return '';
    }
}
