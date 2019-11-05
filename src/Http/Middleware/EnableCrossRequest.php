<?php


namespace Discuz\Http\Middleware;


use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EnableCrossRequest implements MiddlewareInterface
{

    const ALLOW_ORIGIN = [
        'http://editor.swagger.io',
    ];


    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $origin = Arr::get($request->getServerParams(), 'HTTP_ORIGIN') ?? '';

        $response = $handler->handle($request);

        if (in_array($origin, self::ALLOW_ORIGIN)) {
            $response = $response->withAddedHeader('Access-Control-Allow-Origin', $origin);
            $response = $response->withAddedHeader('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
            $response = $response->withAddedHeader('Access-Control-Expose-Headers', 'Authorization, authenticated');
            $response = $response->withAddedHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
            $response = $response->withAddedHeader('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
