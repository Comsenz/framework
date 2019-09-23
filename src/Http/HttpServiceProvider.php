<?php

namespace Discuz\Http;

use Illuminate\Support\ServiceProvider;

class HttpServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(RouteCollection::class, function() {
            return new RouteCollection;
        });

        $this->app->singleton(RouteHandlerFactory::class, function($app) {
            return new RouteHandlerFactory($app);
        });
    }

    public function boot()
    {
    }



}
