<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Qcloud\Services;

use TencentCloud\Cms\V20190321\CmsClient;
use TencentCloud\Cms\V20190321\Models\ImageModerationRequest;
use TencentCloud\Cms\V20190321\Models\TextModerationRequest;

class CmsService extends AbstractService
{
    const ENDPOINT = 'cms.tencentcloudapi.com';

    const REGION = 'ap-guangzhou';

    public function TextModeration($content = '')
    {
        $clientRequest = new TextModerationRequest();
        $clientRequest->fromJsonString(json_encode(['Content' => base64_encode($content)]));
        return $this->client->TextModeration($clientRequest)->serialize();
    }

    public function ImageModeration($params = [])
    {
        $clientRequest = new ImageModerationRequest();
        $clientRequest->fromJsonString(json_encode($params));
        return $this->client->ImageModeration($clientRequest)->serialize();
    }

    protected function getClient()
    {
        return new CmsClient($this->cred, self::REGION, $this->clientProfile);
    }

    protected function setEndpoint()
    {
        return self::ENDPOINT;
    }
}
