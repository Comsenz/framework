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

use Discuz\Http\DiscuzResponseFactory;
use Illuminate\View\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class HandleErrorsWithView implements MiddlewareInterface
{
    protected $log;

    protected $view;

    public function __construct(LoggerInterface $log, Factory $view)
    {
        $this->log = $log;
        $this->view = $view;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // TODO: Implement process() method.
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return $this->showExceptions($e);
        }
    }

    protected function showExceptions(Throwable $error)
    {
        $code = $error->getCode();

        $name = 'errors.'.$code;

        if (!$this->view->exists($name)) {
            $name = 'errors.500';
            $code = 500;
            $this->log->error($error);
        }

        $view = $this->view->make($name);

        return DiscuzResponseFactory::HtmlResponse($view->render(), $code);
    }
}
