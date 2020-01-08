<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api;

use Laminas\Diactoros\Response\JsonResponse;

class JsonApiResponse extends JsonResponse
{
    public function __construct($data, int $status = 200, array $headers = [], int $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        $headers['content-type'] = 'application/vnd.api+json';
        parent::__construct($data, $status, $headers, $encodingOptions);
    }
}
