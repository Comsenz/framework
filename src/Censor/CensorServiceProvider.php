<?php

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: CensorServiceProvider.php xxx 2019-10-15 15:41:00 LiuDongdong $
 */

namespace Discuz\Censor;

use Discuz\Contracts\Setting\SettingRepository;
use Illuminate\Support\ServiceProvider;

class CensorServiceProvider extends ServiceProvider
{
    /**
     * 是否延时加载提供器。
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * 注册服务提供器。
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Censor::class, function ($app) {
            return new Censor($app->make(SettingRepository::class));
        });
    }

    /**
     * 获取提供器提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return [Censor::class];
    }

    public function boot() {

    }
}
