<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http;

use Discuz\Foundation\SiteApp;
use Discuz\Http\Middleware\RequestHandler;
use Throwable;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;
use Laminas\Stratigility\MiddlewarePipe;

class Server extends SiteApp
{
    public function listen()
    {
        $this->siteBoot();

        $pipe = new MiddlewarePipe();

        $pipe->pipe(new RequestHandler([
            '/api' => 'discuz.api.middleware',
            '/' => 'discuz.web.middleware'
        ], $this->app));


        $runner = new RequestHandlerRunner(
            $pipe,
            new SapiEmitter,
            [ServerRequestFactory::class, 'fromGlobals'],
            function (Throwable $e) {
                $generator = new ErrorResponseGenerator;
                return $generator($e, new ServerRequest, new Response);
            }
        );

        $runner->run();
    }
}
