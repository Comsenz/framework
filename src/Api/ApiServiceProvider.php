<?php


namespace Discuz\Api;


use Discuz\Database\DatabaseServiceProvider;
use Discuz\Http\Middleware\DispatchRoute;
use Discuz\Http\RouteCollection;
use Discuz\Http\RouteHandlerFactory;
use Discuz\Http\RouteTrait;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Zend\Stratigility\MiddlewarePipe;

class ApiServiceProvider extends ServiceProvider
{
    use RouteTrait;

    protected $prefixPath = 'api';


    public function register()
    {
        $this->app->singleton('discuz.api.middleware', function($app) {

            $app->register(DatabaseServiceProvider::class);
            $pipe = new MiddlewarePipe();
            return $pipe;
        });

        //保证路由中间件最后执行
        $this->app->afterResolving('discuz.api.middleware', function(MiddlewarePipe $pipe) {
            $pipe->pipe($this->app->make(DispatchRoute::class));
        });
    }

    public function boot() {

        $this->populateRoutes($this->app->make(RouteCollection::class));
    }

    protected function populateRoutes(RouteCollection $route)
    {
        $route->group('/api', function(RouteCollection $route) {
            require $this->app->basePath('routes/api.php');
        });
    }

}
