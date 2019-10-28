<?php

namespace Discuz\Qcloud;

use Illuminate\Support\ServiceProvider;

class QcloudServiceProvider extends ServiceProvider
{

    public function register() {
        $this->app->singleton('qcloud', function ($app) {
            return new QcloudManage($app);
        });
    }
}
