<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Api;

use Discuz\Foundation\Application;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\ServerRequestFactory;

class Client
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function send($controller, $actor, $query, $body): ResponseInterface
    {
        $controller = $this->app->make($controller);
        $request = ServerRequestFactory::fromGlobals(null, $query, $body);
        $request = $request->withAttribute('actor', $actor);

        return $controller->handle($request);
    }
}
