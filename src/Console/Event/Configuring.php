<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Console\Event;

use Discuz\Foundation\Application;
use Illuminate\Console\Command;
use Symfony\Component\Console\Application as ConsoleApplication;

class Configuring
{
    protected $console;
    protected $app;

    public function __construct(ConsoleApplication $console, Application $app)
    {
        $this->console = $console;
        $this->app = $app;
    }

    public function addCommand($command)
    {
        if (\is_string($command)) {
            $command = $this->app->make($command);
        }

        if ($command instanceof Command) {
            $command->setLaravel($this->app);
        }

        $this->console->add($command);
    }
}
