<?php


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
