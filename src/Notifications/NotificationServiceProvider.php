<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Discuz\Notifications;

use Illuminate\Contracts\Notifications\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Notifications\Factory as FactoryContract;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Boot the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ChannelManager::class, function ($app) {
            return new ChannelManager($app);
        });

        $this->app->alias(
            ChannelManager::class,
            DispatcherContract::class
        );

        $this->app->alias(
            ChannelManager::class,
            FactoryContract::class
        );
    }
}
