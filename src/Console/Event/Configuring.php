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
        if (is_string($command)) {
            $command = $this->app->make($command);
        }

        if ($command instanceof Command) {
            $command->setLaravel($this->app);
        }

        $this->console->add($command);
    }
}
