<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http\Exception;

use Exception;

class MethodNotAllowedException extends Exception
{
    public function __construct($message = '', $code = 405, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
