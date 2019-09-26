<?php


namespace Discuz\Api\Middleware;


use Discuz\Api\ErrorHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class HandlerErrors implements MiddlewareInterface
{
    protected $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
       try {
           return $handler->handle($request);
       } catch (Throwable $e) {
           return $this->errorHandler->handler($e);
       }
    }
}
