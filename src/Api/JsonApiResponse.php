<?php

namespace Discuz\Api;

use Zend\Diactoros\Response\JsonResponse;

class JsonApiResponse extends JsonResponse
{
    public function __construct($data, int $status = 200, array $headers = [], int $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        $headers['content-type'] = 'application/vnd.api+json';
        parent::__construct($data, $status, $headers, $encodingOptions);
    }

}
