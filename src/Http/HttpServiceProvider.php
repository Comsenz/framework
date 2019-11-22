<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Http;

use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContracts;
use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(RouteCollection::class, function () {
            return new RouteCollection();
        });

        $this->app->singleton(RouteHandlerFactory::class, function ($app) {
            return new RouteHandlerFactory($app);
        });

        $this->app->singleton(UrlGeneratorContracts::class, function ($app) {
            return new UrlGenerator($app, $app->make(RouteCollection::class));
        });
    }

    public function boot()
    {
    }
}
