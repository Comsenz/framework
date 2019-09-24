<?php


namespace Discuz\Web;


use Discuz\Database\DatabaseServiceProvider;
use Discuz\Http\Middleware\DispatchRoute;
use Discuz\Http\RouteCollection;
use Discuz\Http\RouteHandlerFactory;
use Discuz\Http\RouteTrait;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Zend\Stratigility\MiddlewarePipe;

class WebServiceProvider extends ServiceProvider
{
    use RouteTrait;

    protected $prefixPath = 'web';

    public function register()
    {
        $this->app->singleton('discuz.web.middleware', function($app) {
            $app->register(FilesystemServiceProvider::class);
            $app->register(ViewServiceProvider::class);
            $pipe = new MiddlewarePipe();
            return $pipe;
        });

        //保证路由中间件最后执行
        $this->app->afterResolving('discuz.web.middleware', function(MiddlewarePipe $pipe) {
            $pipe->pipe($this->app->make(DispatchRoute::class));
        });
    }

    public function boot() {
        $this->populateRoutes($this->app->make(RouteCollection::class));
    }

    protected function populateRoutes(RouteCollection $route)
    {
        $route->group('', function(RouteCollection $route) {
            require $this->app->basePath('routes/web.php');
        });
    }
}
