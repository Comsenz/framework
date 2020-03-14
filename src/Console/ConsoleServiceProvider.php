<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
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
