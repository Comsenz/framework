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
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Str::contains($request->getHeaderLine('content-type'), 'json')) {
            $input = json_decode($request->getBody(), true);

            $request = $request->withParsedBody($input ?: []);
        }

        return $handler->handle($request);
    }
}
