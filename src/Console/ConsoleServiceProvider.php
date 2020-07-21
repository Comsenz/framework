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

namespace Discuz\Console;

use Discuz\Console\Event\Configuring;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    protected $commands = [
        SeederMakeCommand::class,
        ScheduleRunCommand::class,
        SeedCommand::class
    ];

    public function boot()
    {
        $events = $this->app['events'];
        $events->listen(Configuring::class, function (Configuring $event) {
            foreach ($this->commands as $command) {
                $event->addCommand($command);
            }
        });
    }
}
