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

use Discuz\Notifications\Services\Database;
use Discuz\Notifications\Services\Wechat;
use Illuminate\Support\Str;
use InvalidArgumentException;

class NotificationManager
{
    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $drivers = [];

    public function createDatabaseDriver()
    {
        return new Database;
    }

    public function createWechatDriver()
    {
        return new Wechat;
    }

    /**
     * Get a driver instance.
     *
     * @param  string|null  $driver
     * @return Database|Wechat
     *
     * @throws \InvalidArgumentException
     */
    public function driver($driver)
    {
        if (is_null($driver)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].',
                static::class
            ));
        }

        if (! isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }

        return $this->drivers[$driver];
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        $method = 'create'.Str::studly($driver).'Driver';

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }
}
