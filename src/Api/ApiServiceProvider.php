<?php


namespace Discuz\Api;

use Discuz\Api\ExceptionHandler\NotAuthenticatedExceptionHandler;
use Discuz\Api\ExceptionHandler\PermissionDeniedExceptionHandler;
use Discuz\Api\ExceptionHandler\RouteNotFoundExceptionHandler;
use Discuz\Api\ExceptionHandler\ServiceResponseExceptionHandler;
use Discuz\Api\ExceptionHandler\TencentCloudSDKExceptionHandler;
use Discuz\Api\ExceptionHandler\ValidationExceptionHandler;
use Discuz\Api\Listeners\AutoResisterApiExceptionRegisterHandler;
use Discuz\Api\Middleware\HandlerErrors;
use Discuz\Api\Events\ApiExceptionRegisterHandler;
use Discuz\Foundation\Application;
use Discuz\Http\Middleware\AuthenticateWithHeader;
use Discuz\Http\Middleware\DispatchRoute;
use Discuz\Http\Middleware\EnableCrossRequest;
use Discuz\Http\Middleware\ParseJsonBody;
use Discuz\Http\RouteCollection;
use Illuminate\Support\ServiceProvider;
use Tobscure\JsonApi\Exception\Handler\FallbackExceptionHandler;
use Zend\Stratigility\MiddlewarePipe;
use Tobscure\JsonApi\ErrorHandler;

class ApiServiceProvider extends ServiceProvider
{

    public function register()
    {

        $this->app->singleton('discuz.api.middleware', function(Application $app) {

            $pipe = new MiddlewarePipe();
            $pipe->pipe($app->make(HandlerErrors::class));
            $pipe->pipe($app->make(ParseJsonBody::class));
            $pipe->pipe($app->make(AuthenticateWithHeader::class));
            $pipe->pipe($app->make(EnableCrossRequest::class));
            return $pipe;
        });

        $this->app->singleton(ErrorHandler::class, function(Application $app) {
            $errorHandler = new ErrorHandler;
            $errorHandler->registerHandler(new RouteNotFoundExceptionHandler());
            $errorHandler->registerHandler(new ValidationExceptionHandler());
            $errorHandler->registerHandler(new NotAuthenticatedExceptionHandler());
            $errorHandler->registerHandler(new PermissionDeniedExceptionHandler());
            $errorHandler->registerHandler(new TencentCloudSDKExceptionHandler());
            $errorHandler->registerHandler(new ServiceResponseExceptionHandler());

            $app->make('events')->dispatch(new ApiExceptionRegisterHandler($errorHandler));

            $errorHandler->registerHandler(new FallbackExceptionHandler($app->config('debug')));
            return $errorHandler;
        });

        //保证路由中间件最后执行
        $this->app->afterResolving('discuz.api.middleware', function(MiddlewarePipe $pipe) {
            $pipe->pipe($this->app->make(DispatchRoute::class));
        });
    }

    public function boot() {

        $this->populateRoutes($this->app->make(RouteCollection::class));

        $this->app->make('events')->listen(ApiExceptionRegisterHandler::class, AutoResisterApiExceptionRegisterHandler::class);
    }

    protected function populateRoutes(RouteCollection $route)
    {
        $route->group('/api', function(RouteCollection $route) {
            require $this->app->basePath('routes/api.php');
        });
    }

}
