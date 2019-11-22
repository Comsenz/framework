<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Console;

use Discuz\Console\Event\Configuring;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $events = $this->app['events'];
        $events->listen(Configuring::class, function (Configuring $event) {
            $event->addCommand(ScheduleRunCommand::class);
        });
    }
}
