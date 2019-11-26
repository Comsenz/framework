<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Web;

use Discuz\Http\Middleware\HandleErrorsWithView;
use Discuz\Http\Middleware\HandleErrorsWithWhoops;
use Discuz\Http\RouteCollection;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Zend\Stratigility\MiddlewarePipe;

class WebServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('discuz.web.middleware', function ($app) {
            $app->register(ViewServiceProvider::class);
            $pipe = new MiddlewarePipe();
            if ($app->config('debug')) {
                $pipe->pipe($app->make(HandleErrorsWithWhoops::class));
            } else {
                $pipe->pipe($app->make(HandleErrorsWithView::class));
            }
            return $pipe;
        });
    }

    public function boot()
    {
        $this->populateRoutes($this->app->make(RouteCollection::class));
    }

    protected function populateRoutes(RouteCollection $route)
    {
        $route->group('', function (RouteCollection $route) {
            require $this->app->basePath('routes/web.php');
        });
    }
}
