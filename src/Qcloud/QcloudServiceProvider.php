<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Qcloud;

use Illuminate\Support\ServiceProvider;

class QcloudServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('qcloud', function ($app) {
            return new QcloudManage($app);
        });
    }
}
