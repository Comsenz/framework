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

namespace Discuz\Api\Listeners;

use Discuz\Api\Events\ApiExceptionRegisterHandler;
use Discuz\Foundation\Application;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;

class AutoResisterApiExceptionRegisterHandler
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(ApiExceptionRegisterHandler $event)
    {
        $exceptions = $this->discoverExceptions((new Finder())->files()->in($this->discoverApiExceptionsWithin()), $this->app->basePath());

        foreach ($exceptions as $exception) {
            $event->apiErrorHandler->registerHandler($exception->newInstance());
        }
    }

    /**
     * @param $files
     * @param $basePath
     * @return array
     * @throws \ReflectionException
     */
    protected function discoverExceptions($files, $basePath)
    {
        $exceptions = [];
        foreach ($files as $file) {
            $class = new ReflectionClass($this->classFromFile($file, $basePath));
            if (!in_array(ExceptionHandlerInterface::class, $class->getInterfaceNames())) {
                continue;
            }
            $exceptions[] = $class;
        }
        return $exceptions;
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $basePath
     * @return string
     */
    protected function classFromFile(SplFileInfo $file, $basePath)
    {
        $class = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        return str_replace(
            [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );
    }

    protected function discoverApiExceptionsWithin()
    {
        $dir = $this->app->path('Api/Exceptions');

        return collect($dir)->reject(function ($directory) {
            return ! is_dir($directory);
        })->toArray();
    }
}
