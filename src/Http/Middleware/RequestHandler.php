<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Discuz\Http\Middleware;

use Discuz\Foundation\Application;
use Discuz\Http\DiscuzResponseFactory;
use Discuz\Http\Exception\NotConfig;
use Discuz\Http\UrlGenerator;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

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
     * @throws NotConfig
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

        return DiscuzResponseFactory::EmptyResponse(500);
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
