<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\Events;

use Laminas\Stratigility\MiddlewarePipe;

class ConfigMiddleware
{
    public $pipe;

    public function __construct(MiddlewarePipe $pipe)
    {
        $this->pipe = $pipe;
    }
}
