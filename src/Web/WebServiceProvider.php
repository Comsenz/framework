<?php


namespace Discuz\Web;


use Discuz\Database\DatabaseServiceProvider;
use Discuz\Http\Middleware\DispatchRoute;
use Discuz\Http\Middleware\HandleErrorsWithView;
use Discuz\Http\Middleware\HandleErrorsWithWhoops;
use Discuz\Http\RouteCollection;
use Discuz\Http\RouteHandlerFactory;
use Discuz\Http\RouteTrait;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Zend\Stratigility\MiddlewarePipe;

class WebServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('discuz.web.middleware', function($app) {
            $app->register(ViewServiceProvider::class);
            $pipe = new MiddlewarePipe();
            if($app->config('debug')) {
                $pipe->pipe($app->make(HandleErrorsWithWhoops::class));
            } else {
                $pipe->pipe($app->make(HandleErrorsWithView::class));
            }
            return $pipe;
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
