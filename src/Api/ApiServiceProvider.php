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

namespace Discuz\Api;

use Discuz\Api\Controller\AbstractSerializeController;
use Discuz\Api\Events\ApiExceptionRegisterHandler;
use Discuz\Api\Events\ConfigMiddleware;
use Discuz\Api\ExceptionHandler\FallbackExceptionHandler;
use Discuz\Api\ExceptionHandler\LoginFailedExceptionHandler;
use Discuz\Api\ExceptionHandler\LoginFailuresTimesToplimitExceptionHandler;
use Discuz\Api\ExceptionHandler\NotAuthenticatedExceptionHandler;
use Discuz\Api\ExceptionHandler\PermissionDeniedExceptionHandler;
use Discuz\Api\ExceptionHandler\RouteNotFoundExceptionHandler;
use Discuz\Api\ExceptionHandler\ServiceResponseExceptionHandler;
use Discuz\Api\ExceptionHandler\TencentCloudSDKExceptionHandler;
use Discuz\Api\ExceptionHandler\ValidationExceptionHandler;
use Discuz\Api\Listeners\AutoResisterApiExceptionRegisterHandler;
use Discuz\Api\Middleware\HandlerErrors;
use Discuz\Api\Middleware\InstallMiddleware;
use Discuz\Foundation\Application;
use Discuz\Http\Middleware\AuthenticateWithHeader;
use Discuz\Http\Middleware\CheckoutSite;
use Discuz\Http\Middleware\CheckUserStatus;
use Discuz\Http\Middleware\DispatchRoute;
use Discuz\Http\Middleware\ParseJsonBody;
use Discuz\Http\Middleware\OptionsRequest;
use Discuz\Http\RouteCollection;
use Illuminate\Support\ServiceProvider;
use Tobscure\JsonApi\ErrorHandler;
use Laminas\Stratigility\MiddlewarePipe;

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('discuz.api.middleware', function (Application $app) {
            $pipe = new MiddlewarePipe();

            if (!$this->app->isInstall()) {
                $pipe->pipe($app->make(InstallMiddleware::class));
                return $pipe;
            }

            $pipe->pipe($app->make(HandlerErrors::class));
            $pipe->pipe($app->make(OptionsRequest::class));
            $pipe->pipe($app->make(ParseJsonBody::class));
            $pipe->pipe($app->make(AuthenticateWithHeader::class));
            $pipe->pipe($app->make(CheckoutSite::class));
            $pipe->pipe($app->make(CheckUserStatus::class));

            $app->make('events')->dispatch(new ConfigMiddleware($pipe));

            return $pipe;
        });

        $this->app->singleton(ErrorHandler::class, function (Application $app) {
            $errorHandler = new ErrorHandler;
            $errorHandler->registerHandler(new RouteNotFoundExceptionHandler());
            $errorHandler->registerHandler(new ValidationExceptionHandler());
            $errorHandler->registerHandler(new NotAuthenticatedExceptionHandler());
            $errorHandler->registerHandler(new PermissionDeniedExceptionHandler());
            $errorHandler->registerHandler(new TencentCloudSDKExceptionHandler());
            $errorHandler->registerHandler(new ServiceResponseExceptionHandler());
            $errorHandler->registerHandler(new LoginFailuresTimesToplimitExceptionHandler());
            $errorHandler->registerHandler(new LoginFailedExceptionHandler());

            $app->make('events')->dispatch(new ApiExceptionRegisterHandler($errorHandler));

            $errorHandler->registerHandler(new FallbackExceptionHandler($app->config('debug')));
            return $errorHandler;
        });

        // 保证路由中间件最后执行
        $this->app->afterResolving('discuz.api.middleware', function (MiddlewarePipe $pipe) {
            $pipe->pipe($this->app->make(DispatchRoute::class));
        });
    }

    public function boot()
    {
        $this->populateRoutes($this->app->make(RouteCollection::class));

        $this->app->make('events')->listen(ApiExceptionRegisterHandler::class, AutoResisterApiExceptionRegisterHandler::class);

        AbstractSerializeController::setContainer($this->app);
    }

    protected function populateRoutes(RouteCollection $route)
    {
        $route->group('/api', function (RouteCollection $route) {
            require $this->app->basePath('routes/api.php');
        });
    }
}
