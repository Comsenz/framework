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

use TencentCloud\Billing\V20180709\BillingClient;
use TencentCloud\Billing\V20180709\Models\DescribeAccountBalanceRequest;

class BillingService extends AbstractService
{
    const ENDPOINT = 'billing.tencentcloudapi.com';

    public function DescribeAccountBalance()
    {
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
