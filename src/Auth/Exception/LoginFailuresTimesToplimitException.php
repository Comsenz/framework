<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Auth\Exception;

use Exception;
use Throwable;

class LoginFailuresTimesToplimitException extends Exception
{
    public function __construct($message = 'Login Times Failures Times Toplimit', $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
