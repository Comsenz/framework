<?php


namespace Discuz\Http\Middleware;


use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ParseJsonBody implements MiddlewareInterface
{


    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Str::contains($request->getHeaderLine('content-type'), 'json')) {
            $input = collect(json_decode($request->getBody(), true));

            $request = $request->withParsedBody($input ?: []);
        }

        return $handler->handle($request);
    }
}
