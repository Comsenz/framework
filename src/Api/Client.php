<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api;

use Discuz\Foundation\Application;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;

class Client
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function send($controller, $actor, $query, $body) : ResponseInterface
    {
        $controller = $this->app->make($controller);

        $psr17Factory = new Psr17Factory();
        $request = (new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory))->fromArrays($_SERVER, [], [], $query, $body);

        $request = $request->withAttribute('actor', $actor);

        return $controller->handle($request);
    }
}
