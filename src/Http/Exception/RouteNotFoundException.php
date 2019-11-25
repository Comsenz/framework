<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http\Exception;

use Exception;

class RouteNotFoundException extends Exception
{
    public function __construct($message = '', $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
