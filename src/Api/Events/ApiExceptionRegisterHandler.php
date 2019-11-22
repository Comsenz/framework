<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Api\Events;

use Tobscure\JsonApi\ErrorHandler;

class ApiExceptionRegisterHandler
{
    public $apiErrorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->apiErrorHandler = $errorHandler;
    }
}
