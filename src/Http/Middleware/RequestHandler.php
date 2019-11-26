<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http\Middleware;

use Discuz\Foundation\Application;
use Discuz\Http\UrlGenerator;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Zend\Diactoros\Response\EmptyResponse;

class RequestHandler implements MiddlewareInterface
{
    protected $middlewares;

    protected $app;

    public function __construct(array $middlewares, Application $app)
    {
        $this->middlewares = $middlewares;
        $this->app = $app;
        krsort($this->middlewares);
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $this->getNormalizedPath($request);
        $requestPath = $request->getUri()->getPath();

        UrlGenerator::setRequest($request);

        foreach ($this->middlewares as $pathPrefix => $middleware) {
            if (strpos($requestPath, $pathPrefix) === 0) {
                $requestHandler = $this->app->make($middleware);

                if ($requestHandler instanceof MiddlewareInterface) {
                    return $requestHandler->process($request, $handler);
                }

                if ($requestHandler instanceof RequestHandlerInterface) {
                    return $requestHandler->handle($request);
                }

                throw new RuntimeException(sprintf('Invalid request handler: %s', gettype($requestHandler)));
            }
        }

        return new EmptyResponse(500);
    }

    private function getNormalizedPath(ServerRequestInterface $request): ServerRequestInterface
    {
        $uri = $request->getUri();

        $baseUrl = Arr::get($request->getServerParams(), 'SCRIPT_NAME');
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $baseUrl = str_replace('\\', '/', $baseUrl);
        }

        $baseUri = basename($baseUrl);

        $baseUrl = rtrim(substr($baseUrl, 0, strlen($baseUrl) - strlen($baseUri)), '/'.\DIRECTORY_SEPARATOR);
        $requestUri = $uri->getPath() ?: '/';

        if ('/' !== $baseUrl && \strlen($requestUri) >= \strlen($baseUrl)) {
            $request = $request->withUri($uri->withPath(substr($requestUri, strlen($baseUrl))));
        }

        return $request;
    }
}
