<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Wechat;

use Illuminate\Support\ServiceProvider;

class WechatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('easyWechat', function ($app) {
            return new EasyWechatManage($app);
        });
    }
}
