<?php

namespace Discuz\Auth\Exception;

use Exception;
use Throwable;

class NotAuthenticatedException extends Exception
{
    public function __construct($message = "", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
